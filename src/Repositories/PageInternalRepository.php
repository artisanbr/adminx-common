<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
use Adminx\Common\Repositories\Base\Repository;
use Adminx\Common\Repositories\Traits\SeoModelRepository;
use Exception;
use Illuminate\Http\UploadedFile;

/**
 * @property ?PageInternal $model
 */
class PageInternalRepository extends Repository
{
    use SeoModelRepository;

    protected string $modelClass = PageInternal::class;


    public function __construct(
        protected ?Page $page = null
    ) {
        parent::__construct();
    }

    public function page(Page $page): static
    {
        $this->page = $page;

        return $this;
    }


    /**
     * @throws Exception
     */
    public function saveTransaction(): ?PageInternal
    {

        $this->model->fill($this->data);
        if($this->page?->id){
            $this->model->page_id = $this->page->id;
        }
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
        /**
         * @var array{cover_file?: UploadedFile, seo: array{image_file?: UploadedFile}} $data
         */

        $this->model->refresh();

        $this->uploadPathBase = $this->model->uploadPathTo();

        //Breadcrumb
        if ($this->data['config']['breadcrumb']['background']['file_upload'] ?? false) {

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->model->config->breadcrumb->background->file ?? null);

            $mediaFile = FileUpload::upload($this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase, 'breadcrumb');

            if ($mediaFile && ($mediaFile->url ?? false)) {
                $this->model->config->breadcrumb->background->url = $mediaFile->url;
                $this->model->save();
                $this->model->refresh();
            }

        }

    }


}
