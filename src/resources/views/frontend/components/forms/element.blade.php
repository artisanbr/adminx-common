@props([
    'element' => new \ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement(),
])


<div class="{{ $element->size_class }} mb-5">
    <x-dynamic-component :component="'forms.'.$element->type->value" :element="$element"/>
</div>
