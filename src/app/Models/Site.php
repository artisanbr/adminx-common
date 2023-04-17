<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Libs\Support\Url;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\Site\SiteConfig;
use ArtisanBR\Adminx\Common\App\Models\Generics\Contact\Contact;
use ArtisanBR\Adminx\Common\App\Models\Generics\Seo;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasRelatedCache;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSEO;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasFiles;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasPosts;
use ArtisanBR\Adminx\Common\App\Rules\DomainRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;

class Site extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasUriAttributes, HasValidation, HasSEO, HasFiles, HasPosts, BelongsToUser, HasOwners, HasPublicIdAttribute, HasRelatedCache;

    protected $connection = 'mysql';

    protected array $ownerTypes = ['user','account'];

    protected $fillable = [
        'user_id',
        'account_id',
        'theme_id',
        'title',
        'url',
        'config',
        'seo',
        'contact',
    ];

    protected $casts = [
        'config' => SiteConfig::class,
        'seo' => Seo::class,
        'contact' => Contact::class,
        'uri' => 'string',
        'dynamic_uri' => 'string',
        'scripts_uri' => 'string',
    ];

    protected $appends = [
        'domain',
        'domain_trust_host_regex',
        'uri',
        'dynamic_uri',
        //'scripts_uri',
    ];

    protected $hidden = [
        'id'
    ];

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array{
        return [
            'title' => ['required'],
            'url' => ['required', new DomainRule],
        ];
    }

    public static function createMessages(): array
    {
        return [
            'title.required' => 'O título do site é obrigatório',
            'url.required' => 'O url do site é obrigatório',
        ];
    }
    //endregion

    //region HELPERS
    public static function getFromPreviousDomain($public_id): EloquentModelBase|Builder|Site|null
    {
        $site = self::where('public_id',$public_id)->with(['pages'])->first();

        if(!$public_id || $site) {
            $previousDomain = Url::previousDomain();

            $site = self::whereUrl($previousDomain)->with(['pages'])->first();
        }

        return $site;
    }
    //endregion

    //region ATTRIBUTES

    protected function domain(): Attribute
    {
        return Attribute::make(get: fn() => Str::of($this->url)->explode('/')->first());
    }

    protected function domainTrustHostRegex(): Attribute
    {
        return Attribute::make(get: fn() => '^(.+\.)?'.preg_quote($this->domain).'$');
    }

    protected function uploadPath(): Attribute
    {
        return Attribute::make(get: fn() => "sites/{$this->public_id}");
    }


    //region GETS

    protected function getHttpProtocolAttribute(){
        return @$this->config->is_https ? 'https' : 'http';
    }

    protected function getUriAttribute()
    {
        return empty($this->url) ? null : (Str::startsWith($this->url, '#') ? $this->url : "{$this->http_protocol}:{$this->dynamic_uri}");
    }

    protected function getUrlAttribute()
    {
        return $this->attributes['url'] ?? null;
    }

    protected function getHomePageAttribute(){
        return $this->pages()->isHome()->first();
    }

    /*protected function getScriptsUriAttribute(){
        return route('api.frontend.scripts.index', $this->public_id);
    }*/

    //endregion

    //region SETS
    protected function setUrlAttribute($value){
        $this->attributes['url'] = Str::replaceNative(['http://', 'https://', ' '], '', $value);
    }
    //endregion

    //endregion

    //region OVERRIDES

    public function save(array $options = [])
    {
        return parent::save($options);
    }

    //endregion

    //region RELATIONS
    public function widgeteables(){
        return $this->hasMany(Widgeteable::class);
    }

    public function users(){
        return $this->belongsToMany(User::class, 'site_users', 'site_id', 'user_id')->using(SiteUser::class);
        //return $this->hasMany(User::class, 'site_id', 'id');
    }

    public function themes(){
        return $this->hasMany(Theme::class);
    }

    public function theme(){
        return $this->hasOne(Theme::class, 'id', 'theme_id');
    }

    public function menus(){
        return $this->hasMany(Menu::class);
    }

    public function lists(){
        return $this->hasMany(CustomList::class, 'site_id', 'id');
    }

    public function pages(){
        return $this->hasMany(Page::class)->orderByDesc('is_home')->orderBy('created_at');
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function tags(){
        return $this->hasMany(Tag::class);
    }

    public function forms(){
        return $this->hasMany(Form::class);
    }

    public function files(){
        return $this->hasMany(File::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_sites', 'site_id', 'account_id')->using(AccountSite::class);
    }

    //endregion
}
