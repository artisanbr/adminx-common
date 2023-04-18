<?php

namespace Adminx\Common\Providers;

use Adminx\Common\Facades\FrontendPage;
use Adminx\Common\Libs\FrontendEngine\FrontendPageEngine;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Site;
use Butschster\Head\Facades\Meta;
use Butschster\Head\Facades\PackageManager;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;
use Butschster\Head\Packages\Package;
use Illuminate\Support\ServiceProvider;

class FrontendPageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        //Singleton: FrontendPage
        $this->app->singleton(FrontendPageEngine::class, function () {
            return new FrontendPageEngine();
        });
        //Bind: FrontendPage
        $this->app->bind('FrontendPageEngine', function () {
            return app()->make(FrontendPageEngine::class);
        });


        $this->app->singleton('CurrentPage', function () {
            return FrontendPage::getCurrentPage();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //

        Meta::macro('registerSeoMetaTagsForSite', function (Site $site) {

            PackageManager::create('site_theme', function (Package $package) use ($site) {


                $packagesInclude = [];

                //Frameworks
                if ($site->theme) {

                    if ($site->theme->config->jquery) {
                        $packagesInclude[] = 'jquery';
                    }

                    if (!$site->theme->config->no_framework) {
                        $packagesInclude[] = $site->theme->config->framework->value;
                    }

                    $packagesInclude = [...$packagesInclude, ...$site->theme->config->plugins->toArray() ?? []];
                }

                $packagesInclude[] = 'frontend.pre';

                $package->requires($packagesInclude);

                /**
                 * @var File $file
                 */
                if ($site->theme) {
                    foreach ($site->theme->files()->themeBundleSortened()->values() as $file) {
                        if ($file->extension === 'css') {
                            //Todo: habilitar DEFER

                            /*[
                                'rel'    => 'stylesheet',
                                'media'  => 'print',
                                'onload' => "this.media='all'",
                            ]*/
                            $package->addStyle($file->name, $file->url);
                        }
                        if ($file->extension === 'js') {
                            $package->addScript($file->name, $file->url, ['defer']);
                        }
                    }
                }
            });

            $metaOg = new OpenGraphPackage('site_og');
            $metaTwitter = new TwitterCardPackage('site_tt');

            $metaOg
                ->setType('website')
                ->setSiteName($site->title)
                ->setLocale('pt_BR');

            $metaTwitter
                //->setSite('@username') todo: user twitter (e redes sociais)
                ->setType('summary');

            $this
                //Site
                ->addMeta('grecaptcha-key', ['content' => $site->config->recaptcha_site_key])
                ->setFavicon($site->theme->media->favicon->file->url ?? '')

                //Packages
                ->registerPackage($metaOg)
                ->registerPackage($metaTwitter)
                ->includePackages(['site_theme', 'frontend.pos']);

        });

        Meta::macro('registerMetaTagsForSiteTheme', function (Site $site) {

            PackageManager::create('site_theme', function (Package $package) use ($site) {


                $packagesInclude = [];

                //Frameworks
                if ($site->theme) {

                    if ($site->theme->config->jquery) {
                        $packagesInclude[] = 'jquery';
                    }

                    if (!$site->theme->config->no_framework) {
                        $packagesInclude[] = $site->theme->config->framework->value;
                    }

                    $packagesInclude = [...$packagesInclude, ...$site->theme->config->plugins->toArray() ?? []];
                }

                $packagesInclude[] = 'frontend.pre';

                $package->requires($packagesInclude);

                /**
                 * @var File $file
                 */
                if ($site->theme) {
                    foreach ($site->theme->files()->themeBundleSortened()->values() as $file) {

                        if ($file->extension === 'css') {
                            //Todo: habilitar DEFER

                            /*[
                                'rel'    => 'stylesheet',
                                'media'  => 'print',
                                'onload' => "this.media='all'",
                            ]*/
                            dump($file->path);
                            $package->addStyle($file->name, $file->url);
                        }
                        if ($file->extension === 'js') {
                            $package->addScript($file->name, $file->url, ['defer']);
                        }
                    }
                }
            });

            $this->includePackages(['site_theme', 'frontend.pos']);

        });

        Meta::macro('registerSeoMetaTagsForPage', function (Page $page) {
            //Page
            $this
                ->prependTitle($page->site->getTitle())
                ->setMetaFrom($page);

        });

        Meta::macro('registerSeoMetaTagsForCategory', function (Page $page, Category $category) {
            $this->setTitle($page->seoTitle("Categoria {$category->title}"));
        });

        Meta::macro('registerSeoMetaTagsForPost', function (Page $page, Post $post) {

            $metaOg = new OpenGraphPackage('site_og');
            $metaTwitter = new TwitterCardPackage('site_tt');

            $seoFullTitle = $post->site->seoTitle($post->getTitle());

            $metaOg
                ->setType('article')
                ->setTitle($seoFullTitle)
                ->setDescription($post->getDescription())
                ->addOgMeta('article:author', $post->user->name)
                ->addOgMeta('article:section', $post->page->title)
                ->addOgMeta('article:tag', $post->getKeywords())
                ->addOgMeta('article:published_time', $post->published_at->toIso8601String())
                ->addOgMeta('article:modified_time', $post->updated_at->toIso8601String())
                ->addOgMeta('og:updated_time', $post->updated_at->toIso8601String())
                ->setUrl($post->uri);

            $metaTwitter
                ->setTitle($seoFullTitle)
                ->setDescription($post->getDescription());

            if ($post->seo_image) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($post->seo->image_uri)
                    ->addMeta('image:alt', $post->getTitle());

                $metaOg->addImage($post->seo->image_uri, [
                    'type' => $post->seo_image->type,
                    'alt'  => $post->getTitle(),
                ]);
            }

            $comments = $post->comments()->paginate(5, ['*'], 'comments_page');

            $this
                ->setMetaFrom($post)
                ->registerPackage($metaOg)
                ->registerPackage($metaTwitter)
                ->setPaginationLinks($comments);
        });
    }
}
