<?php

namespace Adminx\Common\Models\Users;

use Adminx\Common\Models\Account;
use Adminx\Common\Models\AccountUser;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Comment;
use Adminx\Common\Models\Generics\Configs\UserConfig;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\SiteAccessLog;
use Adminx\Common\Models\SiteUser;
use Adminx\Common\Models\Widgets\SiteWidget;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\HasArticles;
use Cog\Laravel\Ban\Traits\Bannable;
use EloquentFilter\Filterable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Laravel\Passport\HasApiTokens;
use Questocat\Referral\Traits\UserReferral;
use Spatie\Permission\Traits\HasRoles;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class User extends EloquentModelBase implements AuthenticatableContract,
                                                AuthorizableContract,
                                                CanResetPasswordContract,
                                                UploadModel,
                                                PublicIdModel
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        MustVerifyEmail,
        HasApiTokens,
        Notifiable,
        HasValidation,
        HasArticles,
        HasPublicIdAttribute,
        HasRoles,
        Bannable,
        HasFactory,
        //HasProfilePhoto,
        Notifiable,
        //TwoFactorAuthenticatable,
        AuthenticationLogable,
        UserReferral,
        Filterable,
        SoftDeletes;

    protected $connection = 'mysql';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'public_id',
        'name',
        'email',
        //'password',
        'new_password',
        'config',
        'avatar_url',
        'site_id',
        'account_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime:d/m/Y H:i:s',
        'created_at'        => 'datetime:d/m/Y H:i:s',
        'config'            => UserConfig::class,
        'avatar_url'        => 'string',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection('mysql');
    }

    //region Validations
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'name'         => ['required'],
            'email'        => ['required', Rule::unique('users', 'email')->ignore($request->id ?? null)],
            'password'     => ['current_password', 'nullable'],
            'new_password' => ['nullable', 'confirmed'],
        ];
    }
    //endregion

    //region HELPERS
    public function uploadPathTo(?string $path = null): string
    {
        return "users/{$this->public_id}" . ($path ? "/{$path}" : '');
    }
    //endregion

    //region Attributes

    //region Sets
    protected function setNewPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
    //endregion

    //region Gets

    public function getGravatarAttribute()
    {
        if(!$this->email){
            return appThemeAsset('media/icons/duotune/communication/com006.svg');
        }

        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "//www.gravatar.com/avatar/$hash";
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->attributes['avatar_url'] ?? $this->gravatar
        );
    }
    //endregion
    //endregion

    //region Overrides
    public function save(array $options = [])
    {
        parent::save($options);

        $this->refresh();
        //Site
        if (Auth::user()) {

            if (Auth::user()->site->id) {
                if ($this->id !== Auth::user()->id && !$this->sites()->where('id', Auth::user()->id)->count()) {
                    $this->sites()->syncWithoutDetaching([Auth::user()->site->id]);
                }
                if (!$this->site_id) {
                    $this->site_id = Auth::user()->site_id;
                }
            }

            //Account
            if (Auth::user()->account->id) {
                if ($this->id !== Auth::user()->id && !$this->accounts()->where('id', Auth::user()->id)->count()) {
                    $this->sites()->syncWithoutDetaching([Auth::user()->account->id]);
                }
                if (!$this->account_id) {
                    $this->account_id = Auth::user()->account->id;
                }
            }
        }

        return parent::save($options);
    }
    //endregion

    //region RELATIONS

    public function site()
    {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_users', 'user_id', 'site_id')->using(SiteUser::class);
    }

    public function sitesAccessLog()
    {
        return $this->belongsToMany(Site::class, 'site_access_log', 'user_id', 'site_id')
                    ->using(SiteAccessLog::class)
                    ->withPivot(['id', 'user_id', 'site_id', 'ip_address', 'created_at', 'updated_at'])
                    ->withTimestamps()
                    ->orderBy('site_access_log.created_at', 'desc')->limit(10);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_users', 'user_id', 'account_id')->using(AccountUser::class);
    }

    /*public function files()
    {
        return $this->hasMany(File::class);
    }*/

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function widgeteables()
    {
        return $this->hasMany(SiteWidget::class);
    }

    //endregion
}
