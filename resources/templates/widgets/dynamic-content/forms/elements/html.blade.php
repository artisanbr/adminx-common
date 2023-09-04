{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
{!! $element->html !!}
