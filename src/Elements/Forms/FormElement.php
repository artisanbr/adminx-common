<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Elements\Forms;

use Adminx\Common\Enums\Forms\FormElementType;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

class FormElement extends GenericModel
{

    protected $fillable = [
        'size_sm',
        'size_md',
        'size_lg',
        'size_xl',
        'size',
        'type',
        'position',
        'title',
        'slug',
        'html',
        'attrs',
        'validation_rules',
    ];

    protected $attributes = [
        'size_sm' => 12,
        'size_md' => 12,
        'size_lg' => 12,
        'size_xl' => 12,
    ];

    protected $casts = [
        'size_sm' => 'int',
        'size_md' => 'int',
        'size_lg' => 'int',
        'size_xl' => 'int',
        'type' => FormElementType::class,
        'title' => 'string',
        'html' => 'string',
        'validation_rules' => 'collection',
        'attrs' => 'collection',
        'custom_sizes' => 'bool',
    ];

    protected $appends = [
        'custom_sizes',
        'size_class',
        'size',
    ];

    //region Validations
    public const AVALIABLE_VALIDATION_RULES = [
        'required' => 'Obrigatório',
        'email' => 'Email',
        'celular_com_ddd' => 'Telefone/Celular',
    ];

    protected function hasValidationRule($rule){
        return $this->validation_rules->contains($rule);
    }
    //endregion

    //region SETS
    public function setSizes($value, array|string $sizes = ['sm','md','lg','xl']): static
    {
        $sizes = Collection::wrap($sizes);

        foreach($sizes as $size){
            $this->attributes["size_{$size}"] = $value;
        }

        return $this;
    }
    protected function setSizeAttribute($value){
        $this->size_sm = $value;

        return $this;
    }
    //endregion

    //region GETS

    protected function getSizeClassAttribute(){
        //todo: considerar framework do tema

        return "col-12 col-sm-{$this->size_sm} col-md-{$this->size_md} col-lg-{$this->size_lg} col-xl-{$this->size_xl}";
    }

    protected function getSizeAttribute(){
        return $this->size_sm;
    }

    protected function getCustomSizesAttribute(){
        return  ($this->size_md ?? $this->size_lg ?? $this->size_xl ?? false) &&
                (
                    $this->size_sm !== $this->size_md ||
                    $this->size_sm !== $this->size_lg ||
                    $this->size_sm !== $this->size_xl ||
                    $this->size_md !== $this->size_lg ||
                    $this->size_md !== $this->size_xl ||
                    $this->size_lg !== $this->size_xl
                );
    }

    protected function getHtmlAttribute($value){
        return match ($this->type){
            FormElementType::HiddenField => "",
            FormElementType::TextField => "",
            FormElementType::TextArea => "",

            //CUSTOM
            default => $value,
        };
    }

    //endregion
}
