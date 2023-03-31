@props([
    'element' => new \ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement(),
])

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
<textarea type="text" class="form-control form-control-lg" id="input_{{ $element->slug }}" name="{{ $element->slug }}"
          placeholder="{{ $element->title }}"></textarea>
