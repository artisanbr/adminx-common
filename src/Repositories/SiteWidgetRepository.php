<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Widgets\Objects\WidgetConfigObject;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigVariable;
use Adminx\Common\Models\Templates\Template;
use Adminx\Common\Models\Widget;
use Adminx\Common\Models\Widgets\SiteWidget;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{media?: object, seo: array{image_file?: UploadedFile}} $data
 * @property ?SiteWidget                                                   $model
 */
class SiteWidgetRepository extends Repository
{
    protected string $modelClass = SiteWidget::class;

    /**
     * Vincular Widget
     */
    public function saveTransaction(): SiteWidget
    {
        ///$this->model->config = @$this->model->widget->config ?? new WidgetConfigObject();

        //Criar vinculo
        $this->model->fill($this->data);
        //$this->model->refresh();
        //$this->model->load('widget');


        $configVariablesValues = $this->data['config']['variables'] ?? [];
        $this->model->config->variables = $this->model->config->variables->map(static function (WidgetConfigVariable $item, $i) use ($configVariablesValues) {
            $item->value = $configVariablesValues[$i]['value'] ?? $item->value;

            return $item;
        })->toArray();

        if (($this->data['sorting_column'] ?? false) && ($this->data['sorting_direction'] ?? false)) {
            $this->model->config->sorting->columns = [$this->data['sorting_column'] => $this->data['sorting_direction']];
        }

        /*if($this->data['no_widget'] ?? false){
            $this->model->widget_id = null;
        }*/

        //$templateContent = $this->data['template_content'];

        $this->model->config->update_template = (string) ($this->data['template_update_type'] ?? '') === '1';

        if($this->data['template_id'] ?? false){
            //Vincular template selecionado
            $templateRelation = $this->model->model_template()->firstOrNew();

            $templateRelation->template_id = $this->data['template_id'];
            $templateRelation->templatable_id = $this->model->id;
            $templateRelation->templatable_type = MorphHelper::getMorphTypeTo(SiteWidget::class);

            $templateRelation->save();

        }else{
            //Excluir relação de templates existentes
            $this->model->model_template()->delete();
        }

        /*if ($this->model->config->update_template) {
            //Atualizar template

            $template = $this->model->template ?? new Template([
                                                                   'site_id'    => $this->model->site_id,
                                                                   'account_id' => $this->model->account_id,
                                                                   'user_id'    => $this->model->user_id,
                                                               ]);

        }
        else {
            //Nova versão
            $template = $this->model->template ? $this->model->template->replicate() : new Template();

            $template->fill([
                                'site_id'    => $this->model->site_id,
                                'account_id' => $this->model->account_id,
                                'user_id'    => $this->model->user_id,
                            ]);

        }

        $template->fill([
                            'content' => $templateContent,
                        ]);

        $template->save();*/




        $this->model->save();

        return $this->model;
    }
}
