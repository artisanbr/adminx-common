<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable $widgeteable
 */
?>
{{--Incluir view do widget--}}
@includeIf("frontend.api.Widgets.{$widgeteable->widget->type->slug}.{$widgeteable->widget->slug}", compact('widgeteable'))
