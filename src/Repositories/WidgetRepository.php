<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Models\Widget;
use Adminx\Common\Models\SiteWidget;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{media?: object, seo: array{image_file?: UploadedFile}} $data
 */
class WidgetRepository extends Repository
{

    protected string $idKey = 'widget_id';

    public function __construct(
        protected Widget|null $widget = null,
    ) {}

    /**
     * Vincular Widget
     */
    public function save(array|Request $data)
    {

        $this->traitData($data);

        return DB::transaction(function () {

            //Criar vinculo
            $widgeteable = new SiteWidget();
            $widgeteable->fill($this->data);

            try{
                $widgeteable->save();
            }catch (Exception $e){
                DB::rollBack();
                dd($widgeteable, $e->getMessage());
            }

            return $widgeteable;
        });
    }


    /**
     * @throws Exception
     */
    public function destroy(array|Request $data): void
    {

        $this->traitData($data);

        dd($this->data);
    }
}
