<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Tools\Import;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Objects\Config\Import\WordpressImportConfig;
use Adminx\Common\Models\Sites\Site;
use Corcel\Model\Builder\PostBuilder;
use Corcel\Model\Builder\TaxonomyBuilder;
use Corcel\Model\Option as wpOption;
use Corcel\Model\Page as WpPage;
use Corcel\Model\Post as WpPost;
use Corcel\Model\Taxonomy;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class WordpressImportTools
{

    public function __construct(
        protected ?Site                  $site = null,
        protected ?Page                  $page = null,
        protected ?WordpressImportConfig $wpImportConfig = null,
    ) {}

    public function setPage(Page $page): self
    {
        $this->page = $page;

        return $this->setSite($page->site);
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this->setConfig($site->config->import->wordpress);
    }

    public function setConfig(WordpressImportConfig $wpImportConfig): self
    {
        $this->wpImportConfig = $wpImportConfig;

        if (!$this->wpImportConfig->checked) {
            $this->checkConnection();
        }
        else {
            $this->prepareConnection();
        }

        return $this;
    }

    public function prepareConnection(): self
    {
        if ($this->wpImportConfig) {
            Config::set('database.connections.corcel', [
                ...config('database.connections.corcel'),
                ...$this->wpImportConfig->toArray(),
            ]);
        }

        return $this;
    }

    public function checkConnection(): ?bool
    {

        if ($this->wpImportConfig?->database && $this->wpImportConfig?->username && $this->wpImportConfig?->password) {
            try {

                $this->prepareConnection();

                $wpCheck = wpOption::get('siteurl');

                if ($wpCheck) {
                    $this->wpImportConfig->checked = true;
                }
                else {
                    $this->wpImportConfig->checked = false;
                }

            } catch (Exception $e) {
                $this->wpImportConfig->checked = false;
            }
        }
        else {
            $this->wpImportConfig->checked = false;
        }

        return $this->wpImportConfig->checked;
    }

    public function getWordpressUri()
    {
        if (!$this->wpImportConfig->checked) {
            $this->checkConnection();
        }
        else {
            $this->prepareConnection();
        }

        return wpOption::get('siteurl');
    }

    //region Queries
    public function wpPostQuery(): PostBuilder
    {
        return WpPost::published()->type('post');
    }

    public function wpPageQuery(): PostBuilder
    {
        return WpPage::published()->type('post');
    }

    public function wpCategoryQuery(): TaxonomyBuilder
    {
        return Taxonomy::category();
    }

    //endregion

    //region WpModels
    /**
     * @param callable(PostBuilder): (LengthAwarePaginator|Collection)|null $query
     *
     * @return array|LengthAwarePaginator|Collection
     */
    public function wpPosts(?callable $query = null): array|LengthAwarePaginator|Collection
    {

        $items = $this->wpPostQuery();

        if (!is_callable($query)) {
            $query = static function (PostBuilder $q) {
                return $q->get();
            };
        }

        $items = $query($items);

        if (get_class($items) === PostBuilder::class) {
            $items = $items->get();
        }

        return $items->map(fn(WpPost $item) => $this->traitWpPostOrPage($item));
    }

    /**
     * @param callable(PostBuilder): (LengthAwarePaginator|Collection)|null $query
     *
     * @return array|LengthAwarePaginator|Collection
     */
    public function wpPages(?callable $query = null): array|LengthAwarePaginator|Collection
    {

        $items = $this->wpPageQuery();

        if (!is_callable($query)) {
            $query = static function (PostBuilder $q) {
                return $q->get();
            };
        }

        $items = $query($items);

        if (get_class($items) === PostBuilder::class) {
            $items = $items->get();
        }

        return $items->map(fn(WpPage $item) => $this->traitWpPostOrPage($item));
    }

    /**
     * @param callable(TaxonomyBuilder): (LengthAwarePaginator|Collection)|null $query
     *
     * @return array|LengthAwarePaginator|Collection
     */
    public function wpCategories(?callable $query = null): array|LengthAwarePaginator|Collection
    {

        $items = $this->wpCategoryQuery();

        if (!is_callable($query)) {
            $query = static function (TaxonomyBuilder $q) {
                return $q->get();
            };
        }

        $items = $query($items);

        if (get_class($items) === PostBuilder::class) {
            $items = $items->get();
        }

        return $items;
    }

    public function traitWpPostOrPage(WpPost|WpPage $wpItem): WpPost|WpPage
    {

        $wpItem->media = HtmlHelper::getMediaUrls($wpItem->content);
        //ID
        $itemRequest = Request::create($wpItem->guid);
        $wpItem->id = $itemRequest->query('page_id');

        if (get_class($wpItem) === WpPage::class) {
            $wpItem->local_page = Page::where('slug', $wpItem->slug)->first();
            $wpItem->local_type = $wpItem->local_page?->type_name ?? null;

            if (!$wpItem->local_type) {
                if (Str::contains($wpItem->slug, ['blog', 'artigos'])) {
                    $wpItem->local_type = 'blog';
                }
                else {
                    $wpItem->local_type = 'custom';
                }
            }
        }

        return $wpItem;
    }
    //endregion

    //region Section Helpers
    public function sectionItems(string $section): array|LengthAwarePaginator|Collection
    {
        $this->prepareConnection();

        return match ($section) {
            'pages' => $this->wpPages(static fn($q) => $q->paginate(10)),
            'categories' => $this->wpCategories(static fn($q) => $q->paginate()),
            'posts' => $this->wpPosts(static fn($q) => $q->paginate()),
            default => collect(),
        };
    }

    public function sectionItemsCount(string $section): int
    {
        $this->prepareConnection();

        return match ($section) {
            'pages' => $this->wpPages()->count(),
            'categories' => $this->wpCategories()->count(),
            'posts' => $this->wpPosts()->count(),
            default => 0,
        };
    }

    public function sectionModels($section): \Illuminate\Database\Eloquent\Collection|array|Collection
    {

        return match ($section) {
            'pages' => Page::all(),
            'categories' => Category::all(),
            'posts' => Article::published()->get(),
            default => collect(),
        };
    }

    public function sectionData($section): array
    {

        return [
            'models' => $this->sectionModels($section),
            'items'  => $this->sectionItems($section),
            'items_count'  => $this->sectionItemsCount($section),
        ];
    }
    //endregion

    //region Import Helpers
    public function importPostsTo(Page $page, $step = 1, $step_items_number = 50): Collection
    {
        if (!$this->page) {
            $this->setPage($page);
        }

        $resultCollection = collect();

        $importPosts = $this->wpPostQuery()->forPage($step, $step_items_number)->get();

        if ($importPosts->count()) {

            foreach ($importPosts as $importPost) {
                $localArticle = $this->page->articles()->where('options');

                if($localParent){
                    $localCategory = $localCategory->whereParentId($localParent->id);
                }

                $localCategory = $localCategory->firstOrNew();

                $localCategory->fill([
                                         'parent_id' => $localParent?->id ?? null,
                                         'title'     => $importCategory->name,
                                         'slug'      => $importCategory->slug,
                                     ]);

                $result = $localCategory->save();

                $resultCollection->add([
                                           'title'  => $importCategory->title,
                                           'slug'   => $importCategory->slug,
                                           'result' => $result,
                                           'moment' => date('d/m/Y - H:i:s'),
                                       ]);

            }
        }

        return $resultCollection;

    }
    public function importCategoriesTo(Site $site, ?Taxonomy $wpParentCategory = null, ?Category $localParent = null): Collection
    {
        if (!$this->site) {
            $this->setSite($site);
        }

        $resultCollection = collect();

        $importCategories = $this->wpCategoryQuery()->where('parent', $wpParentCategory?->term_taxonomy_id ?? 0)->get();

        if ($importCategories->count()) {

            if ($wpParentCategory && !$localParent) {
                $localParent = Category::whereSlug($wpParentCategory->slug)->first();
            }

            foreach ($importCategories as $importCategory) {
                $localCategory = Category::whereSlug($importCategory->slug);

                if($localParent){
                    $localCategory = $localCategory->whereParentId($localParent->id);
                }

                $localCategory = $localCategory->firstOrNew();

                $localCategory->fill([
                                         'parent_id' => $localParent?->id ?? null,
                                         'title'     => $importCategory->name,
                                         'slug'      => $importCategory->slug,
                                     ]);

                $result = $localCategory->save();

                $resultCollection->add([
                                           'title'  => $importCategory->title,
                                           'slug'   => $importCategory->slug,
                                           'result' => $result,
                                           'moment' => date('d/m/Y - H:i:s'),
                                       ]);

                if ($result) {
                    //Get childs
                    $resultCollection = $resultCollection->merge($this->importCategoriesTo($site, $importCategory, $localCategory)->toArray());
                }

            }
        }

        return $resultCollection;

    }
    //endregion
}
