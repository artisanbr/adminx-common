<?php
/***
 * @var \Adminx\Common\Models\SiteWidget $widgeteable
 */
?>
{{--Incluir view do widget--}}
@includeIf("adminx-frontend::api.Widgets.{$widgeteable->widget->type->slug}.{$widgeteable->widget->slug}", compact('widgeteable'))
