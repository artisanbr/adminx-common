<?php

namespace Adminx\Common\Repositories\Base;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class Repository implements RepositoryInterface
{

    public array $data = [];
    protected ?Model $model = null;
    protected string $idKey = 'id';
    protected string $uploadPathBase = '';
    protected string $uploadableType = '';

    protected string $modelClass = EloquentModelBase::class;

    protected function getDataId(){
        return $this->data[$this->idKey] ?? null;
    }


    public function setModel(EloquentModelBase|int $model): void
    {
        if(is_int($model) && $this->modelClass !== EloquentModelBase::class && is_subclass_of(new $this->modelClass(), Model::class)){
            $this->setModel($this->modelClass::findOrNew($model));
        }else{
            $this->model = $model;
        }

    }

    /**
     * Atualizar model
     *
     * @param int           $id
     * @param array|Request $data
     * @param string        $idKey
     *
     * @return mixed
     * @throws Throwable
     */
    public function update(int $id, array|Request $data, string $idKey = 'id')
    {
        $this->idKey = $idKey;
        return $this->save($this->traitData($data, [$idKey => $id]));
    }

    /**
     * Excluir Model
     *
     * @param int           $id
     * @param array|Request $data
     * @param               $idKey
     *
     * @return bool
     * @throws Throwable
     */
    public function delete(int $id, array|Request $data, string $idKey = 'id'): bool
    {
        $this->idKey = $idKey;
        $this->traitData($data, [$idKey => $id]);
        return DB::transaction(fn() => $this->deleteTransaction());
    }

    /**
     * Inserir
     *
     * @param array|Request $data
     * @param string        $idKey
     *
     */
    public function insert(array|Request $data, string $idKey = 'id')
    {
        $this->idKey = $idKey;

        if(!$this->model && $this->modelClass !== EloquentModelBase::class){
            $this->setModel(new $this->modelClass());
        }

        return $this->save($this->traitData($data, [$idKey => null]));
    }

    /**
     * Tratar dados recebidos
     *
     * @param array|Request $data
     * @param array         $mergeData
     *
     * @return array
     */
    public function traitData(array|Request $data, array $mergeData = []): array
    {
        if(!is_array($data) && is_object($data)){
            $data = $data->all();
        }

        $this->data = array_merge_recursive($data, $mergeData);

        if($mergeData[$this->idKey] ?? false){
            $this->data[$this->idKey] = $mergeData[$this->idKey] ?? null;
        }

        if($this->getDataId()){
            $this->setModel($this->getDataId());
        }

        return $this->data;
    }

    /**
     * @param array|Request $data
     *
     * @return mixed
     * @throws Throwable
     */
    public function save(array|Request $data): mixed
    {
        $this->traitData($data);

        return DB::transaction(fn() => $this->saveTransaction());

    }

    /**
     * Executar as operações no banco ao salvar
     * @return mixed
     */
    protected function saveTransaction(): mixed
    {
        return false;
    }

    /**
     * Executar as operações no banco ao excluir
     * @return bool
     */
    protected function deleteTransaction(): bool
    {
        return false;
    }

}
