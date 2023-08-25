<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config\Import;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Pages\Page;
use ArtisanLabs\GModel\GenericModel;
use Corcel\Model\Option as wpOption;
use Corcel\Model\Page as WpPage;
use Corcel\Model\Post as WpPost;
use Corcel\Model\Taxonomy;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class WordpressImportConfig extends GenericModel
{

    protected $fillable = [
        'checked',
        'host',
        'database',
        'username',
        'password',
        'prefix',
        'uri',
        'media_ftp',
    ];

    protected $attributes = [
        'checked'  => false,
        'host'     => 'localhost',
        'database' => 'fvconsultoria_wp',
        'username' => 'fvconsultoria_wp',
        'password' => 'ROGsTKHJFEGq',
        'prefix'   => 'wp_',
        'uri'      => 'https://fvconsultoriaetreinamento.com/',
    ];

    protected $casts = [
        'checked'   => 'bool',
        'host'      => 'string',
        'database'  => 'string',
        'password'  => 'string',
        'username'  => 'string',
        'prefix'    => 'string',
        'uri'       => 'string',
        'media_ftp' => FtpMediaImportConfig::class,
    ];

    public function prepareConnection(): void
    {
        Config::set('database.connections.corcel', [...config('database.connections.corcel'), ...$this->toArray()]);
    }

    public function checkConnection(): ?bool
    {

        if ($this->database && $this->username && $this->password) {
            try {

                $this->prepareConnection();

                $postsCheck = wpOption::get('siteurl');

                if ($postsCheck) {
                    $this->checked = true;
                }
                else {
                    $this->checked = false;
                }

            } catch (Exception $e) {
                $this->checked = false;
            }
        }
        else {
            $this->checked = false;
        }

        return $this->checked;
    }

    public function loadUri()
    {

        $this->prepareConnection();

        $this->attributes['uri'] = wpOption::get('siteurl');

        return $this->uri;
    }

    public function lockPassword(): ?string
    {
        if (!empty($this->password ?? null)) {
            $this->password = Crypt::encrypt($this->password);
        }

        return $this->password;
    }

    public function password_decrypt(): string
    {
        return !empty($this->password ?? null) ? Crypt::decrypt($this->password) : '';
    }

    public static function sectionItems($section): array|LengthAwarePaginator|Collection
    {

        $items = match ($section) {
            'pages' => WpPage::published()->paginate(10),
            'categories' => Taxonomy::category()->paginate(),
            'posts' => WpPost::published()->type('post')->paginate(),
            default => collect(),
        };

        foreach ($items as $item) {
            $item->media = HtmlHelper::getMediaUrls($item->content);
            //ID
            $itemRequest = Request::create($item->guid);
            $item->id = $itemRequest->query('page_id');
            $item->local_page = Page::where('slug', $item->slug)->first();

            if ($section === 'pages') {
                $item->local_type = $item->local_page?->type_name ?? null;

                if (!$item->local_type) {
                    if (Str::contains($item->slug, ['blog', 'artigos'])) {
                        $item->local_type = 'blog';
                    }
                    else {
                        $item->local_type = 'custom';
                    }
                }
            }
        }

        return $items;
    }

    public static function sectionModels($section): \Illuminate\Database\Eloquent\Collection|array|Collection
    {

        return match ($section) {
            'pages' => Page::all(),
            'posts' => Article::published()->get(),
            default => collect(),
        };
    }

    public static function sectionData($section): array
    {

        return [
            'models' => self::sectionModels($section),
            'items'  => self::sectionItems($section),
        ];
    }

    //region Attributes
    //region GET's
    protected function getUriAttribute(){
        //$uri = $this->attributes["uri"] ?? '';
        $uri =  $this->attributes["uri"] ?? '';
        return Str::endsWith($uri, '/') ? $uri : "{$uri}/";
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion
}
