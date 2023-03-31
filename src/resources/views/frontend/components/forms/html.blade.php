@props([
    'element' => new \ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement(),
])

{{--<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>--}}
{!! $element->html !!}
