{{--@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])--}}

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<textarea type="text" class="form-control {{ $element->css_class ?? '' }}" id="input_{{ $element->slug }}" name="{{ $element->slug }}"
          placeholder="{{ $element->title }}"></textarea>
