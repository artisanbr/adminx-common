<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
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

        $theme->assets->resources->css->items = $this->traitRequestDataList($data, 'css');
        $theme->assets->resources->js->items = $this->traitRequestDataList($data, 'js');
        $theme->assets->resources->head_js->items = $this->traitRequestDataList($data, 'head_js');

        //dd($theme->assets->resources->js->items->toArray());

        return $compile ? $theme->saveAndCompile() : $theme->save();

    }
    //endregion

    //region Helpers
    protected function traitRequestDataList($data, $listName = 'css'): Collection
    {

        $listItems = collect($data['items'][$listName] ?? []);

        $listDefers = collect($data['defers'][$listName] ?? []);

        return $listItems->map(function ($path, $position) use ($data, $listName, $listDefers) {
            return [
                'path'     => $path,
                'position' => $position,
                'defer'    => $listDefers->contains($path),
            ];
        });

    }
    //endregion

}
