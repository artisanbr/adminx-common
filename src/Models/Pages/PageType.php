<?php

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\PageConfig;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageType extends EloquentModelBase
{
    use HasSelect2, HasValidation;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'config',
    ];

    protected $casts = [
        'config' => PageConfig::class,
        'has_post' => 'bool'
    ];

    protected $appends = [
        'can_use_forms',
        'can_use_posts'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }


    //region SETS

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: static fn ($value) => Str::contains($value, ' ') ? Str::slug(Str::ucfirst($value)) : Str::ucfirst($value),
        );
    }

    //endregion

    //region GETS

    //region SELECT2
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->title ? "<h3>{$this->title}</h3>" . Str::limit($this->description, 150) : '',
        );
    }
    //endregion

    //region MODULES
    protected function canUsePosts(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->config->canUseModule('posts'));
    }

    protected function usingPosts(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->can_use_posts && $this->config->isUsingModule('posts'));
    }

    protected function canUseForms(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->config->canUseModule('forms'));
    }

    protected function usingForms(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->can_use_forms && $this->config->isUsingModule('forms'));
    }

    protected function canUseList(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->config->canUseModule('list'));
    }

    protected function usingList(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->can_use_list && $this->config->isUsingModule('list'));
    }
    //endregion

    //endregion

    //region OVERRIDES

    public function save(array $options = [])
    {
        //Gerar slug se estiver em branco
        if(empty($this->slug)){
            $this->slug = $this->title;
        }

        return parent::save($options);
    }

    //endregion

    //region RELATIONS

    public function models(){
        return $this->hasMany(PageModel::class, 'page_type_id', 'id');
    }

    public function pages(){
        return $this->hasMany(Page::class, 'type_id', 'id');
    }

    //endregion
}
