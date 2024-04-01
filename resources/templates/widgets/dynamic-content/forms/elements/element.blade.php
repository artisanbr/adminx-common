<div class="{{ $element->size_class }} mb-5">
    @include((@$widget?->template ?? @$template)->getTemplateBladeFile("elements/{$element->type->value}"), compact('element','form'))
</div>
