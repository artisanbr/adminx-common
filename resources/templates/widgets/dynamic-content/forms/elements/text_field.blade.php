{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<input type="text" class="form-control form-control-lg" id="input_{{ $element->slug ?? $ $slug }}" name="{{ $element->slug ?? $slug }}"
       placeholder="{{ $element->title ?? $title }}"/>
