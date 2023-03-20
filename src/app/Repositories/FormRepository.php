<?php

namespace ArtisanBR\Adminx\Common\App\Repositories;

use ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement;
use ArtisanBR\Adminx\Common\App\Libs\Helpers\MorphHelper;
use ArtisanBR\Adminx\Common\App\Models\Form;
use ArtisanBR\Adminx\Common\App\Models\Formulable;
use Illuminate\Support\Facades\DB;
use Throwable;

class FormRepository
{
    /**
     * Salvar um formulário com ou sem atribuição a uma Model
     *
     * @param array $data
     *
     * @return Form|null
     * @throws Throwable
     */
    public function save(array $data): ?Form
    {
        return DB::transaction(function () use ($data) {

            $dataElements = collect($data['elements'])->values();

            $form = Form::findOrNew($data['id'] ?? null);

            $form->fill($data);

            $form->save();
            $form->refresh();

            $form->elements = $form->elements->values()->map(function(FormElement $item, $i) use ($dataElements) {
                $dataItem = $dataElements->get($i);

                if($dataItem['custom_sizes'] !== 'true'){
                    $item->setSizes($dataItem['size']);
                }
                $item->position = $i;
                return $item->toArray();
            })->sortBy('position')->all();

            $form->save();

            //Associate
            $model_type = $data['formulable_type'] ?? $data['model_type'] ?? null;
            $model_id = $data['formulable_id'] ?? $data['model_id'] ?? null;

            if ($model_type && $model_id) {
                $this->assignTo($form->id, $model_type, $model_id);
            }

            return $form;
        });
    }

    /**
     * Associar models
     * @param int $id
     * @param     $model_type
     * @param     $model_id
     *
     * @return mixed
     * @throws Throwable
     */
    public function assignTo(int $id, $model_type, $model_id)
    {
        $model_type = MorphHelper::resolveMorphType($model_type);

        return DB::transaction(function () use ($id, $model_type, $model_id) {
            return Formulable::updateOrCreate([
                'form_id'         => $id,
                'formulable_id'   => $model_id,
                'formulable_type' => $model_type,
            ]);
        });
    }

    /**
     * Desassociar models
     * @param int $id
     * @param     $model_type
     * @param     $model_id
     *
     * @return mixed
     * @throws Throwable
     */
    public function unassignFrom(int $id, $model_type, $model_id)
    {
        $model_type = MorphHelper::resolveMorphType($model_type);

        return DB::transaction(function () use ($id, $model_type, $model_id) {
            return Formulable::where([
                ['form_id', $id],
                ['formulable_id', $model_id],
                ['formulable_type', $model_type],
            ])->delete();
        });
    }

    /**
     * Atualizar um formulário com ou sem atribuição a uma model
     *
     * @param int $id
     * @param     $data
     *
     * @return Form|null
     * @throws Throwable
     */
    public function update(int $id, $data): ?Form
    {
        return $this->save(array_merge($data, ['id' => $id]));
    }

    /**
     * Inserir um formulário com ou sem atribuição a uma model
     *
     * @param     $data
     *
     * @return Form|null
     * @throws Throwable
     */
    public function insert($data): ?Form
    {
        return $this->save(array_merge($data, ['id' => null]));
    }

    /**
     * Salvar um formulário e atribui-lo a uma Model
     *
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Form|null
     * @throws Throwable
     */
    public function saveAndAssignTo(array $data, $model_type, $model_id): ?Form
    {
        return $this->save(array_merge($data, [
            'formulable_type' => $model_type,
            'formulable_id'   => $model_id,
        ]));
    }

    /**
     * Atualizar um formulário e atribui-lo a uma Model
     *
     * @param int   $id
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Form|null
     * @throws Throwable
     */
    public function updateAndAssignTo(int $id, array $data, $model_type, $model_id): ?Form
    {
        return $this->save(array_merge($data, [
            'id'              => $id,
            'formulable_type' => $model_type,
            'formulable_id'   => $model_id,
        ]));
    }

    /**
     * Atualizar um formulário e atribui-lo a uma Model
     *
     * @param array $data
     * @param       $model_type
     * @param       $model_id
     *
     * @return Form|null
     * @throws Throwable
     */
    public function insertAndAssignTo(array $data, $model_type, $model_id): ?Form
    {
        return $this->save(array_merge($data, [
            'id'              => null,
            'formulable_type' => $model_type,
            'formulable_id'   => $model_id,
        ]));
    }
}
