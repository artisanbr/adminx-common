<?php

namespace Adminx\Common\Models\Templates;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Templates\Global\Manager\Facade\PageTemplateManager;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class Template extends EloquentModelBase
{
    use HasSelect2, HasValidation, HasGenericConfig;


    protected $fillable = [
        'user_id',
        'account_id',
        'site_id',
        'public_id',
        'title',
        'description',
        'path',
        'global',
        'morphs',
        'config',
    ];

    protected $casts = [
        //'config' => PageConfig::class
        'public_id' => 'string',
        'title' => 'string',
        'description' => 'string',
        'morphs' => 'collection',
        'global' => 'boolean',
    ];

    protected $appends = [
        //'can_use_forms',
        //'can_use_articles',
    ];

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
        return "{$this->path}/{$file}";
    }

    /*public function getTemplateView($view, $data): \Illuminate\Contracts\View\View
    {
        return View::make($this->getTemplateViewFile($view), $data);
    }*/

    //region Attributes

    protected function fullPath(): Attribute
    {
        $relativePath = ($this->path ? "{$this->path}/" : '').$this->public_id;
        $path = $this->global ? PageTemplateManager::globalTemplatesPath($relativePath) : Storage::path($relativePath);
        return Attribute::make(get: static fn() => $path);
    }
    //region GET's
    //protected function getAttribute(){}
    //endregion

    //region SET's
    //protected function setAttribute(){}

    //endregion
    //endregion

    //region RELATIONS

    public function templatables()
    {
        return $this->morphMany(Templatable::class, 'templatable');
    }

    //endregion
}
