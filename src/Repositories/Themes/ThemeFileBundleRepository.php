<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories\Themes;


use Adminx\Common\Models\Themes\Theme;
use Illuminate\Support\Collection;

class ThemeFileBundleRepository
{

    public function __construct() {}

    //region Actions
    public function saveList(Theme &$theme, $data): bool
    {

        foreach (['css', 'js', 'head_js'] as $collect) {
            /*$theme->assets->resources->{$collect}->fill([
                                                            'items' => $this->traitRequestDataList($data, $collect)->toArray(),
                                                        ]);*/

            //$theme->assets->resources->{$collect}->items = $this->traitRequestDataList($data, $collect)->toArray();

            //dd($theme->toArray());
            //$theme->save();
            //dump($theme->config);
            $theme->fill([
                             'assets' => [
                                 'resources' => [
                                     $collect => [
                                         'items' => $this->traitRequestDataList($data, $collect)->toArray(),
                                     ],
                                 ],
                             ],
                             'config' => [
                                 'bundles_after' => $data['bundles_after'] ?? []
                             ]
                         ]);
            /*$theme->assets->resources->{$collect}->setAttribute('items', $this->traitRequestDataList($data, $collect)->toArray());*/
            //$theme->config->setAttribute('bundles_after', $data['bundle_after'] ?? []);

            //dd($theme->config);



        }


        //$theme->assets->resources->css->items = $this->traitRequestDataList($data, 'css')->toArray();
        //$theme->assets->resources->js->items = $this->traitRequestDataList($data, 'js')->toArray();
        //$theme->assets->resources->head_js->items = $this->traitRequestDataList($data, 'head_js')->toArray();

        //dd($theme->assets->resources->css->items->toArray());

        return $theme->save();

    }
    //endregion

    //region Helpers
    protected function traitRequestDataList($data, $listName = 'css'): Collection
    {

        $listItems = collect($data['items'][$listName] ?? [])->unique();

        //dd($listItems);

        $listLoadModes = collect($data['load_modes'][$listName] ?? []);
        $listBundles = collect($data['bundles'][$listName] ?? []);
        $listDefer = collect($data['defers'][$listName] ?? []);

        return $listItems->map(fn($path, $position) => [
            'path'     => $path,
            'position' => $position,
            //'load_mode' => $listLoadModes->get($path) ?? 'default',
            'bundle'   => $listBundles->contains($path),
            'defer'    => $listDefer->contains($path),
        ])->unique('path')->values();

    }
    //endregion

}
