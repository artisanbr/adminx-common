<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\PageConfig;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasGenericConfig;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PageModel extends EloquentModelBase
{
    use HasSelect2, HasValidation, HasGenericConfig;

    protected $fillable = [
        'page_type_id',
        'title',
        'description',
        'slug',
        'config',
    ];

    protected $casts = [
        'config' => PageConfig::class
    ];

    protected $appends = [
        //'can_use_forms',
        //'can_use_posts',
    ];

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

    protected function canUseForms(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->config->canUseModule('forms') ?: $this->type->can_use_forms ?: false);
    }

    protected function usingForms(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->can_use_forms && ($this->config->isUsingModule('forms') || ($this->type->using_forms ?? false)));
    }

    protected function canUsePosts(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->config->canUseModule('posts') ?: $this->type->can_use_posts ?: false);
    }

    protected function usingPosts(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->can_use_posts && ($this->config->isUsingModule('posts') || ($this->type->using_posts ?? false)));
    }

    protected function canUseList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->config->canUseModule('list') ?: $this->type->can_use_list ?: false);
    }

    protected function usingList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->can_use_list && ($this->config->isUsingModule('list') || ($this->type->using_list ?? false)));
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

    public function type(){
        return $this->belongsTo(PageType::class, 'page_type_id', 'id');
    }

    public function pages(){
        return $this->hasMany(Page::class, 'model_id', 'id');
    }

    //endregion
}
