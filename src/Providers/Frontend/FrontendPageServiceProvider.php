<?php

namespace Adminx\Common\Providers\Frontend;

use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\FrontendEngine\FrontendPageEngine;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\Pages\Modules\Manager\PageModuleManagerEngine;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageModel;
use Adminx\Common\Models\Templates\Global\Manager\PageTemplateManagerEngine;
use Adminx\Common\Models\Pages\Types\Manager\PageTypeManagerEngine;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;
use App\Http\Controllers\Frontend\Page\PageModelController;
use Butschster\Head\Facades\Meta as MetaFacade;
use Butschster\Head\MetaTags\Meta;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FrontendPageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->registerEngine();
        $this->registerPageManagers();

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        $this->setupMetaMacros();
    }


    protected function setupMetaMacros()
    {
        Meta::macro('registerFromSite', function (Site $site) {

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

                //Packages
                ->registerPackage($metaOg)
                ->registerPackage($metaTwitter);

        });

        Meta::macro('registerFromSiteTheme', function (Theme $theme) {

            $theme->registerMetaPackage();


            $this->setFavicon($theme->media->favicon->url ?? '');

            $this->includePackages([$theme->meta_pkg_name, 'frontend.pos']);

        });

        Meta::macro('registerSeoForPage', function (Page $page) {

            if ($page->site->seo->config->show_parent_title) {
                $this->prependTitle($page->site->getTitle());
            }

            //Page
            $this->setMetaFrom($page);

        });

        MetaFacade::macro('registerSeoMetaTagsForCategory', function (Page $page, Category $category) {
            $this->setTitle($page->seoTitle("Categoria {$category->title}"));
        });

        Meta::macro('registerSeoForArticle', function (Article $article) {

            $metaOg = new OpenGraphPackage('site_og_article');
            $metaTwitter = new TwitterCardPackage('site_tt_article');

            $site = FrontendSite::current();
            $page = FrontendPage::current();

            $seoFullTitle = $site->seoTitle($page->seoTitle($article->getTitle()));

            $metaOg
                ->setType('article')
                ->setTitle($seoFullTitle)
                ->setDescription($article->getDescription())
                //->addOgMeta('article:author', $article->user->name)
                ->addOgMeta('article:section', $page->title)
                ->addOgMeta('article:tag', $article->getKeywords())
                ->addOgMeta('article:published_time', $article->published_at->toIso8601String())
                ->addOgMeta('article:modified_time', $article->updated_at->toIso8601String())
                ->addOgMeta('og:updated_time', $article->updated_at->toIso8601String())
                ->setUrl($article->uri);

            $metaTwitter
                ->setTitle($seoFullTitle)
                ->setDescription($article->getDescription());

            if ($article->seo_image) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($article->seo->image_uri)
                    ->addMeta('image:alt', $article->getTitle());

                $metaOg->addImage($article->seo->image_uri, [
                    'type' => $article->seo_image->type,
                    'alt'  => $article->getTitle(),
                ]);
            }

            $comments = $article->comments()->paginate(5, ['*'], 'comments_page');

            $this
                ->setMetaFrom($article)
                ->setTitle($seoFullTitle)
                ->setDescription($article->getDescription())
                ->registerPackage($metaOg)
                ->setPaginationLinks($comments)
                ->registerPackage($metaTwitter);
        });

        MetaFacade::macro('registerSeoForPageModel', function (PageModel $pageModel, $modelItem = null) {

            $metaOg = new OpenGraphPackage('site_og_page_model');
            $metaTwitter = new TwitterCardPackage('site_tt_page_model');


            $seoFullTitle = $pageModel->page->site->seoTitle($pageModel->page->seoTitle($modelItem->title ?? null));

            $metaOg
                ->setType('article')
                ->setTitle($seoFullTitle)
                ->setDescription($pageModel->page->getDescription())
                ->setUrl($pageModel->uriTo($modelItem->url));

            $metaTwitter
                ->setTitle($seoFullTitle)
                ->setDescription($pageModel->page->getDescription());

            if ($pageModel->breadcrumb_config->background_url) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($pageModel->breadcrumb_config->background_url)
                    ->addMeta('image:alt', $modelItem->title ?? $pageModel->page->title);

                $metaOg->addImage($pageModel->breadcrumb_config->background_url, [
                    'type' => $pageModel->breadcrumb_config->background->type,
                    'alt'  => $modelItem->title ?? $pageModel->page->title,
                ]);
            }

            $this
                ->setTitle($seoFullTitle)
                ->setDescription($pageModel->page->getDescription())
                ->registerPackage($metaOg)
                ->registerPackage($metaTwitter);
        });
    }


    /**
     * Registrar Facades e Singletons da Engine de Páginas
     */
    protected function registerEngine(): void
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
     * Registrar Facades e Singletons do Managers das Páginas (tipos, modulos e templates)
     */
    protected function registerPageManagers(): void
    {

        //region PageType

        //Singleton: PageTypeManagerEngine
        $this->app->singleton(PageTypeManagerEngine::class, function () {
            return new PageTypeManagerEngine();
        });
        $this->app->bind('PageTypeManagerEngine', function () {
            return app()->make(PageTypeManagerEngine::class);
        });

        //endregion

        //region PageTemplate

        //Singleton: PageTypeManagerEngine
        $this->app->singleton(PageTemplateManagerEngine::class, function () {
            return new PageTemplateManagerEngine();
        });
        $this->app->bind('PageTemplateManagerEngine', function () {
            return app()->make(PageTemplateManagerEngine::class);
        });

        //endregion

        //Singleton: PageModuleManagerEngine
        $this->app->singleton(PageModuleManagerEngine::class, function () {
            return new PageModuleManagerEngine();
        });
        $this->app->bind('PageModuleManagerEngine', function () {
            return app()->make(PageModuleManagerEngine::class);
        });

        //endregion

    }


}
