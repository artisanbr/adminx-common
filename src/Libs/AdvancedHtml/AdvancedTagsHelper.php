<?php

namespace Adminx\Common\Libs\AdvancedHtml;

use Adminx\Common\Models\Site;
use Adminx\Common\Models\SiteWidget;
use Illuminate\Contracts\Support\Arrayable;

class AdvancedTagsHelper
{

    public static function listSiteTags(Site $site)
    {

        $site->load(['theme']);

        $siteCollection = collect($site->toArray())->except(['config', 'theme', 'widgeteables']);

        $siteTags = collect([
                                ['title' => 'Site',  'category' => 'site'],
                            ]);


        $processData = function (Arrayable|array $data, $category, $icon, $path = null) use (&$processData) {

            if (!($path ?? false)) {
                $path = $category;
            }

            $tagsCollection = collect();

            foreach ($data as $attr => $value) {

                $finalPath = $path ? "{$path}.{$attr}" : $attr;

                if (!is_array($value)) {

                    $translatePath = "attributes/{$finalPath}";

                    $translation = __($translatePath);

                    if ($translation != $translatePath) {

                        $tagsCollection->add([
                                                 'id'       => $finalPath,
                                                 'text'     => "<h4 class='text-gray-800'>{$translation}</h4><code class='small'>{{ {$finalPath} }}</code>",
                                                 'category' => $category,
                                                 'icon'     => $icon,
                                             ]);
                    }

                }
                else {
                    $tagsCollection = $tagsCollection->merge($processData($value, $category, $icon, $finalPath));
                }

            }

            return $tagsCollection->values();

        };

        $siteTags = $siteTags
            ->merge($processData($siteCollection, 'site', config('adminx.defines.kicons.references.site')))
            ->values()
            ->merge([
                        ['title' => 'Tema',  'category' => 'theme'],
                    ])
            ->values()
            ->merge($processData($site->theme->toArray(), 'theme', config('adminx.defines.kicons.references.theme')))
            ->values();

        //Widgets
        if ($site->widgeteables()->count()) {
            $widgeteables = $site->widgeteables->map(fn($widgeteable) => [
                'id'       => "widget('{$widgeteable->public_id}')",
                'text'     => "<h4 class='text-gray-800'>{$widgeteable->title}</h4><code class='small'>{{ widget('{$widgeteable->public_id}') }}</code>",
                'category' => 'widget',
                'icon'     => config('adminx.defines.kicons.references.widget'),
            ])->values();
            $siteTags = $siteTags->merge([
                                             ['title' => 'Widgets',  'category' => 'widget'],
                                         ])
                                 ->values()
                                 ->merge($widgeteables)->values();
        }


        //Menus
        if ($site->menus()->count()) {
            $menus = $site->menus->map(fn($menu) => [
                'id'       => "menu('{$menu->slug}')",
                'text'     => "<h4 class='text-gray-800'>{$menu->title}</h4><code class='small'>{{ menu('{$menu->slug}') }}</code>",
                'category' => 'menu',
                'icon'     => config('adminx.defines.kicons.references.menu', 'menu'),
            ])->values();

            $siteTags = $siteTags->merge([
                                             ['title' => 'Menus',  'category' => 'menu'],
                                         ])
                                 ->values()
                                 ->merge($menus);
        }

        return $siteTags->values();

    }
}
