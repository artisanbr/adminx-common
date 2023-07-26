<?php

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PageType extends Pivot
{
    use HasSelect2, HasValidation, BelongsToPage;

    public $incrementing = true;
    public $timestamps   = true;

    protected $fillable = [
        'page_id',
        'model_type',
        'model_id',
        'slug',
        'content',
        'config',
    ];

    protected $casts = [
        //'config' => PageConfig::class,
        'slug'     => 'string',
        'content' => FrontendHtmlObject::class,
    ];

    protected $appends = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    //region SETS

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: static fn($value) => Str::contains($value, ' ') ? Str::slug(Str::ucfirst($value)) : Str::ucfirst($value),
        );
    }

    //endregion

    //region GETS

    //region SELECT2
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->model->title ?? false) ? "<h3>{$this->model->title}</h3>" : '',
        );
    }
    //endregion

    //endregion

    //region OVERRIDES

    public function save(array $options = [])
    {
        //Gerar slug se estiver em branco
        if (empty($this->slug) && ($this->model->title ?? false)) {
            $this->slug = $this->model->title;
        }

        return parent::save($options);
    }

    //endregion

    //region RELATIONS

    public function model()
    {
        return $this->morphTo();
    }

    //endregion
}
