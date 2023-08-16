<?php

namespace Adminx\Common\Models;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Libs\Support\Url;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Generics\Configs\Site\SiteConfig;
use Adminx\Common\Models\Generics\Contact\Contact;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Seo\SiteSeo;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Adminx\Common\Models\Traits\HasSEO;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasArticles;
use Adminx\Common\Models\Widgets\SiteWidget;
use Adminx\Common\Rules\DomainRule;
use Adminx\Common\Models\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Adminx\Common\Models\Users\User;

class Site extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
{
    use HasUriAttributes, HasValidation, HasSEO, HasFiles, HasArticles, BelongsToUser, HasOwners, HasPublicIdAttribute, HasRelatedCache;

    protected $connection = 'mysql';

    protected array $ownerTypes = ['user', 'account'];

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
        'config'      => SiteConfig::class,
        'seo'         => SiteSeo::class,
        'contact'     => Contact::class,
        'uri'         => 'string',
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
        'id',
        'user_id',
        'account_id',
        'theme_id',
    ];
    //protected $with   = ['theme'];

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title' => ['required'],
            'url'   => ['required', new DomainRule],
        ];
    }

    public static function createMessages(): array
    {
        return [
            'title.required' => 'O título do site é obrigatório',
            'url.required'   => 'O url do site é obrigatório',
        ];
    }
    //endregion

    //region HELPERS
    public function uploadPathTo(?string $path = null): string
    {
        return "sites/{$this->public_id}" . ($path ? "/{$path}" : '');
    }

    public static function getFromPreviousDomain($public_id): EloquentModelBase|Builder|Site|null
    {
        $site = self::where('public_id', $public_id)->first();

        if (!$public_id || $site) {
            $previousDomain = Url::previousDomain();

            $site = self::whereUrl($previousDomain)->first();
        }

        return $site;
    }

    public function getBuildViewData(array $merge_data = []): array
    {
        $requestData = request()->all() ?? [];

        $this->load(['theme']);

        $viewData = [
            'site'        => $this,
            'theme'       => $this->theme,
            'searchTerm'  => '',
            'breadcrumb' => $this->theme->config->breadcrumb,
        ];

        if ($requestData['q'] ?? false) {
            $viewData['searchTerm'] = $requestData['q'];
            $viewData['breadcrumbs'] = ["Resultados da pesquisa: " . $requestData['q']];
        }


        //Meta::registerSeoForPage($this);

        return [...$viewData, ...$merge_data];
    }

    public function frontendBuild(): FrontendBuildObject
    {
        $frontendBuild = new FrontendBuildObject();

        //JSON-LD
        $frontendBuild->head->addAfter($this->ld_json_script);

        return $frontendBuild;
    }


    //endregion

    //region ATTRIBUTES


    protected function ldJsonScript(): Attribute
    {
        //Todo: busca personalizada https://developers.google.com/search/docs/appearance/site-names?hl=pt-br

        $script = HtmlHelper::ldJsonScript([
                                               "@context"      => "https://schema.org",
                                               "@type"         => "WebSite",
                                               "name"          => $this->title,
                                               "alternateName" => $this->seo->title,
                                               "url"           => $this->uri,
                                           ]);

        $script .= HtmlHelper::ldJsonScript([
                                                "@context" => "https://schema.org",
                                                "@type"    => "Organization",
                                                "url"      => $this->uri,
                                                "logo"     => $this->theme->media->logo->uri,
                                            ]);

        return Attribute::make(
            get: fn() => $script,
        );
    }

    protected function domain(): Attribute
    {
        return Attribute::make(get: fn() => Str::of($this->url)->explode('/')->first());
    }

    protected function domainTrustHostRegex(): Attribute
    {
        return Attribute::make(get: fn() => '^(.+\.)?' . preg_quote($this->domain) . '$');
    }

    protected function uploadPath(): Attribute
    {
        return Attribute::make(get: fn() => "sites/{$this->public_id}");
    }


    //region GETS

    protected function getHttpProtocolAttribute()
    {
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

    protected function getHomePageAttribute()
    {
        return $this->pages()->isHome()->first();
    }

    /*protected function getScriptsUriAttribute(){
        return route('api.frontend.scripts.index', $this->public_id);
    }*/

    //endregion

    //region SETS
    protected function setUrlAttribute($value)
    {
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
    public function widgets()
    {
        return $this->hasMany(SiteWidget::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'site_users', 'site_id', 'user_id')->using(SiteUser::class);
        //return $this->hasMany(User::class, 'site_id', 'id');
    }

    public function usersAccessLog()
    {
        return $this->belongsToMany(User::class, 'site_access_log', 'site_id', 'user_id')
                    ->using(SiteAccessLog::class)
                    ->withPivot(['id', 'user_id', 'site_id', 'ip_address', 'created_at', 'updated_at'])
                    ->withTimestamps()
                    ->orderBy('site_access_log.created_at', 'desc')->limit(10);
    }

    public function themes()
    {
        return $this->hasMany(Theme::class);
    }

    public function theme()
    {
        return $this->hasOne(Theme::class, 'id', 'theme_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function lists()
    {
        return $this->hasMany(CustomList::class, 'site_id', 'id');
    }

    public function pages()
    {
        return $this->hasMany(Page::class)->orderByDesc('is_home')->orderBy('created_at');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function comments()
    {
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
