<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Throwable;

class MenuItemRepository
{
    public int|null $menu_id;

    public function menu($menu_id): static
    {
        $this->menu_id = $menu_id;
        return $this;
    }

    /**
     * Salvar um item com ou sem atribuição a uma Model
     * @param array $data
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function save(array $data): ?MenuItem
    {
        return DB::transaction(function() use($data){
            $menuItem = MenuItem::findOrNew($data['id'] ?? null);
            $menuItem->fill($data);
            $menuItem->menu_id = $this->menu_id;

            //Associate
            $menuable_type = $data['menuable_type'] ?? null;
            $menuable_id = $data["menuable_type_{$menuable_type}_id"] ?? null;

            if($menuable_type && $menuable_type !== 'link' && $menuable_type !== 'menu'){

                $menuable_type = MorphHelper::resolveMorphType($menuable_type);

                $menuItem->menuable_type = $menuable_type;
                $menuItem->menuable_id = $menuable_id ?? null;
                $menuItem->save();

            }else{
                $menuItem->menuable_type = $menuable_type;
                $menuItem->menuable_id = null;

                if($menuable_type === 'menu'){
                    $menuItem->url = null;
                }
            }

            $menuItem->menu_id = $this->menu_id;
            $menuItem->newPosition();

            $menuItem->save();

            return $menuItem;
        });
    }

    /**
     * Atualizar um item com ou sem atribuição a uma model
     * @param int $id
     * @param     $data
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function update(int $id, $data): ?MenuItem
    {
        return $this->save(array_merge($data, ['id' => $id]));
    }

    /**
     * Inserir um item com ou sem atribuição a uma model
     * @param     $data
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function insert($data): ?MenuItem
    {
        return $this->save(array_merge($data, ['id' => null]));
    }

    /**
     * Salvar um item e atribui-lo a uma Model
     * @param array $data
     * @param       $menuable_type
     * @param       $menuable_id
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function saveAndAssignTo(array $data, $menuable_type, $menuable_id): ?MenuItem
    {
        return $this->save(array_merge($data, [
            'menuable_type' => $menuable_type,
            'menuable_id' => $menuable_id
        ]));
    }

    /**
     * Atualizar um item e atribui-lo a uma Model
     * @param int   $id
     * @param array $data
     * @param       $menuable_type
     * @param       $menuable_id
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function updateAndAssignTo(int $id, array $data, $menuable_type, $menuable_id): ?MenuItem
    {
        return $this->save(array_merge($data, [
            'id' => $id,
            'menuable_type' => $menuable_type,
            'menuable_id' => $menuable_id
        ]));
    }

    /**
     * Atualizar um item e atribui-lo a uma Model
     * @param array $data
     * @param       $menuable_type
     * @param       $menuable_id
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function insertAndAssignTo(array $data, $menuable_type, $menuable_id): ?MenuItem
    {
        return $this->save(array_merge($data, [
            'id' => null,
            'menuable_type' => $menuable_type,
            'menuable_id' => $menuable_id
        ]));
    }

    /**
     * @param array{id: string, order: string, parentId?: string} $items
     *
     * @return bool
     */
    public function updateList(array $items): bool{
        $retorno = true;
        foreach ($items as $item){
            $menuItem = MenuItem::find($item['id']);

            if($menuItem) {
                $menuItem->parent_id = $item['parentId'] ?? null;
                $menuItem->position = $item['order'];

                $retorno = $menuItem->save();
            }else{
                $retorno = false;
            }

            if(!$retorno){
                return $retorno;
                break;
            }
        }

        return $retorno;
    }
}
