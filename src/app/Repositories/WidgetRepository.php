<?php

namespace ArtisanBR\Adminx\Common\App\Repositories;

use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Widgets\WidgetMediaElement;
use ArtisanBR\Adminx\Common\App\Models\Widget;
use ArtisanBR\Adminx\Common\App\Models\Widgeteable;
use ArtisanBR\Adminx\Common\App\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use function App\Repositories\dd;

/**
 * @property  array{media?: WidgetMediaElement, seo: array{image_file?: UploadedFile}} $data
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
    public function linkTo(array|Request $data)
    {

        $this->traitData($data);

        return DB::transaction(function () {

            //Criar vinculo
            $widgeteable = new Widgeteable();
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
    public function unlinkTo(array|Request $data): void
    {

        $this->traitData($data);

        dd($this->data);
    }
}
