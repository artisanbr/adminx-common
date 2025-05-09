{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

@php
    $element = $element ?? new \Adminx\Common\Elements\Forms\FormElement();
    $optionList = $optionList ?? $element->option_list ?? [];
 @endphp

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<label for="input_{{ $element->slug ?? $slug }}" class="mb-1">
    {{ $element->title ?? $title }}
</label>
<select class="form-select form-select fw-bold {{ $element->css_class ?? '' }}" id="input_{{ $element->slug ?? $slug }}" name="{{ $element->slug ?? $slug }} {{ $element->required ? 'required' : '' }}"
       placeholder="{{ $element->title ?? $title }}">
    <option value="">Selecione</option>
    @foreach(($optionList) as $option)
        <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
    @endforeach
</select>