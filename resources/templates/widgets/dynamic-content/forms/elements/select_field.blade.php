{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<label for="input_{{ $element->slug ?? $slug }}" class="mb-1">
    {{ $element->title ?? $title }}
</label>
<select class="form-select form-select-lg form-select fw-bold" id="input_{{ $element->slug ?? $slug }}" name="{{ $element->slug ?? $slug }}"
       placeholder="{{ $element->title ?? $title }}">
    <option value="">Selecione</option>
    @foreach(($element->option_list ?? $optionList ?? []) as $option)
        <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
    @endforeach
</select>