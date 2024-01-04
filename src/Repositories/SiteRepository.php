<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Models\Sites\Objects\Config\SiteConfig;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Repositories\Base\Repository;
use Adminx\Common\Repositories\Traits\SeoModelRepository;
use Exception;
use Illuminate\Http\UploadedFile;

/**
 * @property  array{config?: SiteConfig, seo: array{image_file?: UploadedFile}} $data
 * @property ?Site                                                              $model
 *
 */
class SiteRepository extends Repository
{
    use SeoModelRepository;

    protected string $modelClass = Site::class;

    /**
     * Salvar Tema
     *
     * @throws Exception
     */
    public function saveTransaction(): ?Site
    {

        $this->model->fill($this->data);

        $this->model->config->mail->lockPassword();
        $this->model->config->mail->checkConnection();

        $this->model->save();

        $this->processUploads();

        return $this->model;
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->model) {
            abort(404);
        }

        $this->model->refresh();

        $this->uploadPathBase = $this->model->uploadPathTo();

        //$this->uploadPathBase = "pages/{$this->model->public_id}/";

        $seoFile = $this->processSeoUploads();

        //Imagem SEO
        if ($seoFile) {
            $this->model->seo->image_url = $seoFile->url;
            $this->model->save();
        }

    }
}
