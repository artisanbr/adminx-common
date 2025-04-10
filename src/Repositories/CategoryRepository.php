<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Categorizable;
use Adminx\Common\Models\Category;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryRepository
{
    /**
     * Salvar uma categoria com ou sem atribuição a uma Model
     * @param array $data
     *
     * @return Category|null
     * @throws Throwable
     */
    public function save(array $data): ?Category
    {
        return DB::transaction(function() use($data){
            $category = Category::findOrNew($data['id'] ?? null);
            $category->fill($data);
            $category->save();

            //Associate
            $model_type = $data['categorizable_type'] ?? $data['model_type'] ?? null;
            $model_id = $data['categorizable_id'] ?? $data['model_id'] ?? null;

            if($model_type && $model_id){

                $model_type = MorphHelper::resolveMorphType($model_type);

                $relation_data = [
                    'category_id' => $category->id,
                    'categorizable_id' => $model_id,
                    'categorizable_type' => $model_type
                ];

                /*$category->categorizable()->whereHasMorph('model', $model_type, function(Builder $query) use($model_id){
                    $query->where('categorizable_id', $model_id);
                })->firstOrCreate($relation_data);*/

                Categorizable::updateOrCreate($relation_data);
            }

            return $category;
        });
    }

    /**
     * Atualizar uma categoria com ou sem atribuição a uma model
     * @param int $id
     * @param     $data
     *
     * @return Category|null
     * @throws Throwable
     */
    public function update(int $id, $data): ?Category
    {
        return $this->save(array_merge($data, ['id' => $id]));
    }

    /**
     * Inserir uma categoria com ou sem atribuição a uma model
     * @param     $data
     *
     * @return Category|null
     * @throws Throwable
     */
    public function insert($data): ?Category
    {
        return $this->save(array_merge($data, ['id' => null]));
    }

    /**
     * Salvar uma categoria e atribui-la a uma Model
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Category|null
     * @throws Throwable
     */
    public function saveAndAssignTo(array $data, $model_type, $model_id): ?Category
    {
        return $this->save(array_merge($data, [
            'categorizable_type' => $model_type,
            'categorizable_id' => $model_id
        ]));
    }

    /**
     * Atualizar uma categoria e atribui-la a uma Model
     * @param int   $id
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Category|null
     * @throws Throwable
     */
    public function updateAndAssignTo(int $id, array $data, $model_type, $model_id): ?Category
    {
        return $this->save(array_merge($data, [
            'id' => $id,
            'categorizable_type' => $model_type,
            'categorizable_id' => $model_id
        ]));
    }

    /**
     * Atualizar uma categoria e atribui-la a uma Model
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Category|null
     * @throws Throwable
     */
    public function insertAndAssignTo(array $data, $model_type, $model_id): ?Category
    {
        return $this->save(array_merge($data, [
            'id' => null,
            'categorizable_type' => $model_type,
            'categorizable_id' => $model_id
        ]));
    }

    public function destroy($id): bool
    {
        return !blank($id) ? DB::transaction(function() use($id){
            $category = Category::find($id);
            //Delete categorizables
            //$category->categorizables()->delete();
            return $category?->delete() ?? false;
        }) : false;
    }
}
