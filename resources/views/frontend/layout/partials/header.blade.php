<?php
/**
 * @var \Adminx\Common\Models\Sites\Site                                        $site
 * @var \Adminx\Common\Models\Themes\Theme                                       $theme
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */
?>
{!! $theme->assets->js->before_body->html ?? '' !!}
@{{ frontendBuild.body.before }}
{{--Theme Header--}}
{!! $theme->header->html ?? '' !!}