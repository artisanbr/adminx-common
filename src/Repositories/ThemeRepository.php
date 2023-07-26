<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeMediaElement;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Theme;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{media?: ThemeMediaElement, seo: array{image_file?: UploadedFile}} $data
 * @property ?Theme $model
 */
class ThemeRepository extends Repository
{

    protected string $modelClass = Theme::class;

    public function __construct(
    ) {}

    /**
     * Salvar Tema
     */
    public function saveTransaction(): ?Theme
    {
        //$this->model->header->is_html_advanced = $this->data['header']['is_html_advanced'];
        //$this->model->footer->is_html_advanced = $this->data['footer']['is_html_advanced'] ?? false;


        $this->model->fill($this->data);

        //dd($this->data, $this->model->footer, $this->model->footer->is_html_advanced, $this->model->footer->raw);

        if(!$this->model->config->breadcrumb){
            $this->model->config->breadcrumb = new BreadcrumbConfig();
        }

        $this->model->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];

        $this->model->save();
        $this->model->refresh();

        if($this->data['is_main'] ?? false){
            $this->model->refresh();
            $this->model->site->theme_id = $this->model->id;
            $this->model->site->save();
        }


        $this->processUploads();
        $this->model->saveAndCompile();

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

        $this->uploadPathBase = $this->model->uploadPathTo('media');

        //$this->uploadPathBase = "themes/{$this->model->public_id}";
        //$this->uploadableType = MorphHelper::resolveMorphType($this->model);

        //Media

        if($this->data['config']['breadcrumb']['background']['file_upload'] ?? false){

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->model->config->breadcrumb->background->file ?? null);

            $breadcrumbFile = FileUpload::upload($this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase, 'breadcrumb');

            if(!$breadcrumbFile){
                abort(500);
            }

            $this->model->config->breadcrumb->background->url = $breadcrumbFile->url;
        }

        if ($this->data['media'] ?? false) {

            //Passar em todas as medias
            foreach ($this->data['media'] as $attribute => $media) {

                //Verifica se o arquivo foi enviado
                if($media['file_upload'] ?? false){

                    //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $media['file_upload'], $this->uploadPathBase . 'media', $attribute, $this->model->media->{$attribute}->file ?? null);

                    $mediaFile = FileUpload::upload($media['file_upload'], $this->uploadPathBase, $attribute);

                    if(!$mediaFile){
                        abort(500);
                    }

                    $this->model->media->{$attribute}->url = $mediaFile->url;
                }
            }
        }

        //Todo: Assets
    }
}
