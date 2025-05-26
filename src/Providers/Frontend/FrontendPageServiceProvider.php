<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Providers\Frontend;

use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\FrontendEngine\FrontendPageEngine;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Pages\Modules\Manager\PageModuleManagerEngine;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\Types\Manager\PageTypeManagerEngine;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Templates\Global\Manager\GlobalTemplateManagerEngine;
use Adminx\Common\Models\Themes\Theme;
use Butschster\Head\Facades\Meta as MetaFacade;
use Butschster\Head\MetaTags\Meta;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;
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

            $this->setFavicon($theme->media->favicon->url ?? '');

            $theme->registerMetaPackage();


           $this->includePackages([$theme->meta_pkg_name]);
           //$this->includePackages([$theme->meta_pkg_name, 'frontend.pos']);

        });

        Meta::macro('registerSeoForPage', function (Page $page) {

            if ($page->site->seo->config->show_parent_title) {
                $this->prependTitle("{{ site.seoTitle() }}");
            }

            $this->setMetaFrom($page);

            $metaOg = new OpenGraphPackage('site_og_article');
            $metaTwitter = new TwitterCardPackage('site_tt_article');

            if ($page->seoImage()) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($page->seoImage())
                    ->addMeta('image:alt', $page->seoTitle());

                $metaOg->addImage($page->seoImage(), [
                    'type' => 'image',
                    'alt'  => $page->seoTitle(),
                ]);
            }

            $metaOg
                ->setType('page')
                ->setTitle($page->seoTitle())
                ->setDescription($page->seoDescription())
                //->setUrl($article->uri)
                //->addOgMeta('article:author', $article->user->name)
                ->addOgMeta('og:updated_time', $page->updated_at->toIso8601String());

            $metaTwitter
                ->setTitle($page->seoTitle())
                ->setDescription($page->seoDescription());


            //Page
            $this
                //->setPaginationLinks($articles) todo
                ->registerPackage($metaOg)
                ->registerPackage($metaTwitter);

        });

        MetaFacade::macro('registerSeoMetaTagsForCategory', function (Page $page, Category $category) {
            $this->setTitle($page->seoTitle("Categoria {$category->title}"));
        });

        Meta::macro('registerSeoForArticle', function (Article $article) {

            $site = FrontendSite::current() ?? $article->site;
            $page = FrontendPage::current() ?? $article->page;

            if ($site->seo->config->show_parent_title) {
                $this->prependTitle("{{ site.seoTitle() }} - {{ page.seoTitle() }}");
            }

            //$article->load(['site','page']);
            $metaOg = new OpenGraphPackage('site_og_article');
            $metaTwitter = new TwitterCardPackage('site_tt_article');


            $seoFullTitle = $article->seoTitle(); //$site->seoTitle($page->seoTitle($article->seoTitle()));

            $metaOg
                ->setType('article')
                ->setTitle($seoFullTitle)
                ->setDescription($article->seoDescription())
                //->setUrl($article->uri)
                //->addOgMeta('article:author', $article->user->name)
                ->addOgMeta('article:section', $page->title)
                ->addOgMeta('article:tag', $article->getKeywords())
                ->addOgMeta('article:published_time', $article->published_at->toIso8601String())
                ->addOgMeta('article:modified_time', $article->updated_at->toIso8601String())
                ->addOgMeta('og:updated_time', $article->updated_at->toIso8601String());

            $metaTwitter
                ->setTitle($seoFullTitle)
                ->setDescription($article->seoDescription());

            if ($article->seoImage()) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($article->seoImage())
                    ->addMeta('image:alt', $article->seoTitle());

                $metaOg->addImage($article->seoImage(), [
                    'type' => 'image',
                    'alt'  => $article->seoTitle(),
                ]);
            }

            $comments = $article->comments()->paginate(5, ['*'], 'comments_page');

            $this
                ->setMetaFrom($article)
                ->setTitle($seoFullTitle)
                ->setDescription($article->seoDescription())
                ->setKeywords($article->getKeywords())
                ->registerPackage($metaOg)
                ->setPaginationLinks($comments)
                //->setCanonical($article->uri)
                ->registerPackage($metaTwitter);
        });


        Meta::macro('registerSeoObject', function (Seo $seo, Site $site = null) {

            //$site = $site ?? FrontendSite::current() ?? auth()->user()->site;

            //$article->load(['site','page']);
            $metaOg = new OpenGraphPackage('site_og_seo');
            $metaTwitter = new TwitterCardPackage('site_tt_seo');

            if (!blank($seo->title_prefix)) {
                $this->prependTitle($seo->title_prefix);
            }


            //$seoFullTitle = $seo->title; //$site->seoTitle($page->seoTitle($article->seoTitle()));

            $metaOg
                ->setType($seo->document_type ?? 'website')
                ->setTitle($seo->title ?? '')
                ->setDescription($seo->description ?? '')
                //->setUrl($article->uri)
                //->addOgMeta('article:author', $article->user->name)
                ->addOgMeta('article:section', $seo->title ?? '')
                ->addOgMeta('article:tag', $seo->keywords ?? '')
                ->addOgMeta('article:published_time', $seo->published_at ?? '')
                ->addOgMeta('article:modified_time', $seo->updated_at ?? '')
                ->addOgMeta('og:updated_time', $seo->updated_at ?? '');

            $metaTwitter
                ->setTitle($seo->title ?? '')
                ->setDescription($seo->description ?? '');

            if ($seo->image_url) {
                $metaTwitter
                    ->setType('summary_large_image')
                    ->setImage($seo->image_url)
                    ->addMeta('image:alt', $seo->title);

                $metaOg->addImage($seo->image_url, [
                    'type' => 'image',
                    'alt'  => $seo->title,
                ]);
            }


            $this
                ->setTitle($seo->title ?? '')
                ->setDescription($seo->description ?? '')
                ->setKeywords($seo->keywords ?? '')
                ->registerPackage($metaOg)
                //->setCanonical($article->uri)
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
        $this->app->singleton(GlobalTemplateManagerEngine::class, function () {
            return new GlobalTemplateManagerEngine();
        });
        $this->app->bind('GlobalTemplateManagerEngine', function () {
            return app()->make(GlobalTemplateManagerEngine::class);
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
