@props([
    'element' => new \ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement(),
])

<label for="input_{{ $element->slug }}" class="form-label">{{ $element->title }}</label>
<input type="file" class="form-control form-control-lg border-light" id="input_{{ $element->slug }}"
       name="{{ $element->slug }}" placeholder="{{ $element->title }}"/>
