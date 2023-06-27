<?php

namespace Adminx\Common\Repositories\Traits;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\Objects\FileObject;
use Adminx\Common\Repositories\Base\Repository;
use Illuminate\Http\UploadedFile;

trait SeoModelRepository
{

    protected ?UploadedFile $seoUploadFile;

    protected function processSeoUploads(): false|FileObject|null
    {
        /**
         * @var Repository $this
         */

        if (!empty($this->uploadPathBase)) {

            $seoUploadFile = $this->seoUploadFile ?? $this->data['seo']['image_file'] ?? false;


            //Imagem SEO
            if ($seoUploadFile) {
                return FileUpload::upload($seoUploadFile, $this->uploadPathBase, 'seo');
            }

        }

        return false;
    }
}