<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Article;
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
    protected string $modelClass = SiteWidget::class;

    public function __construct(
    ) {}

    /**
     * Vincular Widget
     */
    public function saveTransaction(): SiteWidget
    {

        //Criar vinculo
        $this->model->fill($this->data);

        $this->model->save();

        return $this->model;
    }
}
