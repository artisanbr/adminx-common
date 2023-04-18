@props([
    'element' => new \Adminx\Common\Elements\Forms\FormElement(),
])


<div class="{{ $element->size_class }} mb-5">
    <x-dynamic-component :component="'frontend::forms.'.$element->type->value" :element="$element"/>
</div>
