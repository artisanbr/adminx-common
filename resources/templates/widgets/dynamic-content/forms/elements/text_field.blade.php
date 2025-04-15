@php use Adminx\Common\Elements\Forms\FormElement; @endphp
<?php
/**
 * @var FormElement $element  
 * 
 */
?>


{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}
{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<input type="text" class="form-control {{ $element->css_class ?? '' }}" id="input_{{ $element->slug ?? $ $slug }}" name="{{ $element->slug ?? $slug }}"
       placeholder="{{ $element->title ?? $title }}" {{ $element->required ? 'required' : '' }}/>
