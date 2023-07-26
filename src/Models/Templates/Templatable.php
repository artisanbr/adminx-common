<?php

namespace Adminx\Common\Models\Templates;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\View;

class Templatable extends Pivot
{

    public $incrementing = true;

    protected $table = 'templatables';

    protected $fillable = [
        'id',
        'template_id',
        'templatable_id',
        'templatable_type',
        'config',
    ];

    protected $casts = [
        //'config' => PageConfig::class
    ];

    protected $appends = [
        //'can_use_forms',
        //'can_use_articles',
    ];

    protected $with = ['template'];

    //region SELECT2
    /*protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->title ? "<h3>{$this->title}</h3>" . Str::limit($this->description, 150) : '',
        );
    }*/
    //endregion

    public function getTemplateFile($file): string
    {
        return $this->template->getTemplateFile($file);
    }

    //region Attributes

    protected function path(): Attribute
    {
        return Attribute::make(get: fn() => $this->template->full_path);
    }
    //region GET's
    //protected function getAttribute(){}
    //endregion

    //region SET's
    //protected function setAttribute(){}

    //endregion
    //endregion


    //endregion

    //region OVERRIDES

    //endregion

    //region RELATIONS

    // Relacionamento com a tabela Template
    public function templatable()
    {
        return $this->morphTo();
    }

    // Relacionamento com a tabela Page
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    //endregion
}
