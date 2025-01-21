{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>
<input type="file" class="form-contro border-light {{ $element->css_class ?? '' }}" id="input_{{ $element->slug }}"
       name="{{ $element->slug }}" placeholder="{{ $element->title }}"/>
