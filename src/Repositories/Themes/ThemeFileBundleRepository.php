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
    public function saveList(Theme &$theme, $data, $compile = true): bool
    {

        //dump($data['defers']);

        foreach (['css','js','head_js'] as $collect) {
            $theme->assets->resources->{$collect}->fill([
                                                            'items' => $this->traitRequestDataList($data, $collect)->toArray()
                                                        ]);

        }



        //$theme->assets->resources->css->items = $this->traitRequestDataList($data, 'css')->toArray();
        //$theme->assets->resources->js->items = $this->traitRequestDataList($data, 'js')->toArray();
        //$theme->assets->resources->head_js->items = $this->traitRequestDataList($data, 'head_js')->toArray();

        //dd($theme->assets->resources->css->items->toArray());

        return $compile ? $theme->saveAndCompile() : $theme->save();

    }
    //endregion

    //region Helpers
    protected function traitRequestDataList($data, $listName = 'css'): Collection
    {

        $listItems = collect($data['items'][$listName] ?? [])->unique();

        //dd($listItems);

        $listLoadModes = collect($data['load_modes'][$listName] ?? []);

        return $listItems->map(function ($path, $position) use ($data, $listName, $listLoadModes) {
            return [
                'path'     => $path,
                'position' => $position,
                'load_mode'    => $listLoadModes->get($path) ?? 'default',
                //'defer'    => $listDefers->contains($path),
            ];
        })->unique('path')->values();

    }
    //endregion

}
