<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\View;

class Formulable extends Pivot
{
    protected $table = 'formulables';

    public $timestamps = false;

    protected ViewContract|null $viewCache = null;

    //region HELPERS
    public function formView($view = 'frontend.forms'): ViewContract
    {
        if (!$this->viewCache || $this->viewCache->name() !== $view) {
            $this->viewCache = View::make($view, [
                'model' => $this->model,
                'form'  => $this->form,
            ]);
        }

        return $this->viewCache;
    }
    //endregion

    //region ATTRIBUTES
    protected function html(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->formView()->render() ?? ''
        );
    }

    protected function js(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->formView()->renderSections()['js'] ?? ''
        );
    }
    //endregion

    //region RELATIONS

    public function form()
    {
        return $this->belongsTo(Form::class)->with(['site']);
    }

    public function model()
    {
        return $this->morphTo('model', 'formulable_type', 'formulable_id');
    }

    //endregion
}
