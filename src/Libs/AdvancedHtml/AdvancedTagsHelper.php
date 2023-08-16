<?php

namespace Adminx\Common\Libs\AdvancedHtml;

use Adminx\Common\Models\Site;
use Adminx\Common\Models\Widgets\SiteWidget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Blade;

class AdvancedTagsHelper
{

    public static function listSiteTags(Site $site)
    {

        $site->load(['theme']);

        $siteCollection = collect($site->toArray())->except(['config', 'theme', 'widgeteables']);

        $siteTags = collect([
                                ['title' => 'Site', 'category' => 'site'],
                            ]);

        $getIcon = fn($icon) => Blade::render('<x-kicon :icon="$icon" size="2" color="primary" />', compact('icon'));


        $processData = function (Arrayable|array $data, $category, $icon, $path = null) use (&$processData, $getIcon) {

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
                                                 'icon'     => $getIcon($icon),
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
            ->merge($processData($siteCollection, 'site', 'site'))
            ->values()
            ->merge([
                        ['title' => 'Tema', 'category' => 'theme'],
                    ])
            ->values()
            ->merge($processData($site->theme->toArray(), 'theme', 'theme'))
            ->values();

        //Widgets
        if ($site->widgets()->count()) {
            $widgeteables = $site->widgets->map(fn(SiteWidget $siteWidget) => [
                'id'       => "widget('{$siteWidget->public_id}')",
                'text'     => "<h4 class='text-gray-800'>{$siteWidget->title}</h4><code class='small'>{{ widget('{$siteWidget->public_id}') }}</code>",
                'category' => 'widget',
                'icon'     => $getIcon('widget'),
            ])->values();
            $siteTags = $siteTags->merge([
                                             ['title' => 'Widgets', 'category' => 'widget'],
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
                'icon'     => $getIcon('menu'),
            ])->values();

            $siteTags = $siteTags->merge([
                                             ['title' => 'Menus', 'category' => 'menu'],
                                         ])
                                 ->values()
                                 ->merge($menus);
        }


        //CustomLists
        if ($site->lists()->count()) {
            $customLists = $site->lists->map(fn($customList) => [
                'id'       => "custom_list('{$customList->public_id}')",
                'text'     => "<h4 class='text-gray-800'>{$customList->title}</h4><code class='small'>{{ custom_list('{$customList->public_id}') }}</code>",
                'category' => 'custom_list',
                'icon'     => $getIcon('list'),
            ])->values();

            $siteTags = $siteTags->merge([
                                             ['title' => 'Listas Personalizadas', 'category' => 'custom_list'],
                                         ])
                                 ->values()
                                 ->merge($customLists);
        }

        return $siteTags->values();

    }
}
