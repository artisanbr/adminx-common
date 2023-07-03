<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\MenuItem;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Repositories\Base\Repository;
use Illuminate\Support\Facades\DB;
use Throwable;

/***
 * @property ?MenuItem $model
 */
class MenuItemRepository extends Repository
{
    public int|null $menu_id;

    protected string $modelClass = MenuItem::class;

    public function menu($menu_id): static
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    /**
     * Salvar um item com ou sem atribuição a uma Model
     *
     * @param array $data
     *
     * @return MenuItem|null
     * @throws Throwable
     */
    public function saveTransaction(): ?MenuItem
    {
        $this->model->fill($this->data);
        $this->model->menu_id = $this->menu_id;

        //Associate
        $menuable_type = $data['menuable_type'] ?? null;
        $menuable_id = $data["menuable_type_{$menuable_type}_id"] ?? null;

        if ($menuable_type && $menuable_type !== 'link' && $menuable_type !== 'menu') {

            $menuable_type = MorphHelper::resolveMorphType($menuable_type);

            $this->model->menuable_type = $menuable_type;
            $this->model->menuable_id = $menuable_id ?? null;
            $this->model->save();

        }
        else {
            $this->model->menuable_type = $menuable_type;
            $this->model->menuable_id = null;

            if ($menuable_type === 'menu') {
                $this->model->url = null;
            }
        }

        $this->model->menu_id = $this->menu_id;
        $this->model->newPosition();

        $this->model->save();

        return $this->model;
    }



    /**
     * Salvar um item e atribui-lo a uma Model
     *
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
            'menuable_id'   => $menuable_id,
        ]));
    }

    /**
     * Atualizar um item e atribui-lo a uma Model
     *
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
            'id'            => $id,
            'menuable_type' => $menuable_type,
            'menuable_id'   => $menuable_id,
        ]));
    }

    /**
     * Atualizar um item e atribui-lo a uma Model
     *
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
            'id'            => null,
            'menuable_type' => $menuable_type,
            'menuable_id'   => $menuable_id,
        ]));
    }

    /**
     * @param array{id: string, order: string, parentId?: string} $items
     *
     * @return bool
     */
    public function updateList(array $items): bool
    {
        $retorno = true;
        foreach ($items as $item) {
            $this->model = MenuItem::find($item['id']);

            if ($this->model) {
                $this->model->parent_id = $item['parentId'] ?? null;
                $this->model->position = $item['order'];

                $retorno = $this->model->save();
            }
            else {
                $retorno = false;
            }

            if (!$retorno) {
                return $retorno;
                break;
            }
        }

        return $retorno;
    }
}
