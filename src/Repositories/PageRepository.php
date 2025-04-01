<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Repositories\Base\Repository;
use Adminx\Common\Repositories\Traits\SeoModelRepository;
use Exception;
use Illuminate\Http\UploadedFile;

/**
 * @property  array{config?: PageConfig, seo: array{image_file?: UploadedFile}} $data
 * @property ?Page                                                              $model
 *
 */
class PageRepository extends Repository
{
    use SeoModelRepository;

    protected string $modelClass = Page::class;

    /**
     * Salvar Tema
     *
     * @throws Exception
     */
    public function saveTransaction(): ?Page
    {


        $this->model->fill($this->data);


        $this->model->save();
        $this->model->refresh();
        //dd('chegou', $this->model);


        //region Defaults

        //region Template
        //Se um template for selecionado, definir como o principal
        if ($this->data['template_id'] ?? false) {
            $pageTemplate = $this->model->page_template()->firstOrNew([
                                                                          'templatable_type' => 'page',
                                                                      ]);

            $pageTemplate->template_id = $this->data['template_id'];
            $pageTemplate->save();
        }
        else if ($this->model->page_template()->count()) {
            $this->model->page_template()->delete();
        }
        //endregion

        //region Generate breadcrumb
        $this->model->config->breadcrumb = $this->model->config->breadcrumb ?? $this->model->site->theme->config->breadcrumb ?? new BreadcrumbConfig();

        $this->model->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];
        //endregion

        //Modules

        //endregion


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
