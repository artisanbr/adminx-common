<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Article;
use Adminx\Common\Repositories\Base\Repository;
use Adminx\Common\Repositories\Traits\SeoModelRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{config?: PageConfig, seo: array{image_file?: UploadedFile}} $data
 * @property ?Page                                                              $model
 *
 */
class PageRepository extends Repository
{
    use SeoModelRepository;

    protected string $modelClass = Page::class;

    public function __construct(
        protected Page|null $page = null,
    ) {}

    /**
     * Salvar Tema
     */
    public function saveTransaction(): ?Page
    {

        //$this->setModel(Page::findOrNew($this->data[$this->idKey] ?? null));

        //$this->theme->header->is_html_advanced = $this->data['header']['is_html_advanced'];
        //$this->theme->footer->is_html_advanced = $this->data['footer']['is_html_advanced'] ?? false;


        $this->model->fill($this->data);


        $this->model->save();
        $this->model->refresh();
        //dd('chegou', $this->model);


        //region Defaults

        //Generate breadcrumb

        $this->model->config->breadcrumb = $this->model->config->breadcrumb ?? $this->model->site->theme->config->breadcrumb ?? new BreadcrumbConfig();

        $this->model->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];

        //Modules
        $this->model->config->allowed_modules = $this->model->type->allowed_modules->toArray();

        //endregion

        //$this->model->config->useModule('data_source', (bool) ($this->model->config->source->id ?? false));

        $this->model->save();

        $this->processUploads();
        $this->model->save();

        return $this->model;
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->model || !$this->model->site) {
            abort(404);
        }

        $this->model->refresh();

        $this->uploadPathBase = $this->model->uploadPathTo();

        //$this->uploadPathBase = "pages/{$this->model->public_id}/";

        $seoFile = $this->processSeoUploads();

        //Breadcrumb
        if ($this->data['config']['breadcrumb']['background']['file_upload'] ?? false) {

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->model->config->breadcrumb->background->file ?? null);

            $mediaFile = FileUpload::upload($this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase, 'breadcrumb');

            if (!$mediaFile) {
                abort(500);
            }

            $this->model->config->breadcrumb->background->url = $mediaFile->url;

            if (!$seoFile) {
                $this->model->seo->image_url = $mediaFile->url;
            }
        }

        //Imagem SEO
        if ($seoFile) {
            $this->model->seo->image_url = $seoFile->url;
        }

        //Todo: Assets
    }
}
