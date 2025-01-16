<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories\Themes;


use Adminx\Common\Models\Themes\Theme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ThemeFileBundleRepository
{

    public function __construct() {}

    //region Actions
    public function saveList(Theme &$theme, $data): bool
    {

        $theme->config->bundles_after = $data['bundles_after'] ?? [];
        $theme->save();


        foreach (['css', 'js', 'head_js'] as $collect) {
            /*$theme->assets->resources->{$collect}->fill([
                                                            'items' => $this->traitRequestDataList($data, $collect)->toArray(),
                                                        ]);*/

            //$theme->assets->resources->{$collect}->items = $this->traitRequestDataList($data, $collect)->toArray();

            //dd($theme->toArray());
            //$theme->save();
            //dump($theme->assets->resources->{$collect}, $this->traitRequestDataList($data, $collect)->toArray());

            /*$theme->update([
                               'assets' => $theme->assets->fill([
                                                                    'resources' => [
                                                                        $collect => [
                                                                            'items' => $this->traitRequestDataList($data, $collect)->toArray(),
                                                                        ],
                                                                    ],
                                                                ])->toArray(),
                           ]);*/

            /*$theme->assets->fill([
                                     'resources' => [
                                         $collect => [
                                             'items' => $this->traitRequestDataList($data, $collect)->toArray(),
                                         ],
                                     ],
                                 ]);*/

            $theme->assets->resources->{$collect}->setAttribute('items', $this->traitRequestDataList($data, $collect)->toArray());




        }

        return DB::table('themes')->where('id', $theme->id)->update([
            'assets->resources' => $theme->assets->resources->toJson(),
        ]) > 0;
        /*return $theme->update([
                           'assets->resources' => $theme->assets->resources->toArray(),
                       ]);*/
        /* $theme->assets->resources->fill([
                                             'head_js' => [
                                                 'items' => $this->traitRequestDataList($data, 'head_js')->toArray(),
                                             ],
                                             'js'      => [
                                                 'items' => $this->traitRequestDataList($data, 'js')->toArray(),
                                             ],
                                             'css'     => [
                                                 'items' => $this->traitRequestDataList($data, 'css')->toArray(),
                                             ],
                                         ]);*/

    }
    //endregion

    //region Helpers
    protected function traitRequestDataList($data, $listName = 'css'): Collection
    {


        $listItems = collect($data['items'][$listName] ?? [])->unique();


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
