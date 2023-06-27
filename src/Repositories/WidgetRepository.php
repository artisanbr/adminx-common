<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Models\Post;
use Adminx\Common\Models\Widget;
use Adminx\Common\Models\SiteWidget;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{media?: object, seo: array{image_file?: UploadedFile}} $data
 * @property ?SiteWidget $model
 */
class WidgetRepository extends Repository
{

    public function __construct(
    ) {}

    /**
     * Vincular Widget
     */
    public function saveTransaction(): SiteWidget
    {

        //Criar vinculo
        $this->setModel(SiteWidget::findOrNew($this->getDataId()));
        $this->model->fill($this->data);

        $this->model->save();

        return $this->model;
    }
}
