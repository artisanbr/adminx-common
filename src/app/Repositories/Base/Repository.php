<?php

namespace ArtisanBR\Adminx\Common\App\Repositories\Base;

use Illuminate\Http\Request;

abstract class Repository implements RepositoryInterface
{

    public array $data = [];
    protected string $idKey = 'id';
    protected string $uploadPathBase = '';
    protected string $uploadableType = '';


    /**
     * Atualizar
     *
     * @param int           $id
     * @param array|Request $data
     * @param string        $idKey
     *
     * @return null
     */
    public function update(int $id, array|Request $data, string $idKey = 'id')
    {
        $this->idKey = $idKey;
        return $this->save($this->traitData($data, [$idKey => $id]));
    }

    /**
     * Excluir
     * @param int           $id
     * @param array|Request $data
     * @param               $idKey
     *
     * @return bool
     */
    public function delete(int $id, array|Request $data, string $idKey = 'id'): bool
    {
        $this->idKey = $idKey;
        $this->traitData($data, [$idKey => $id]);
        return true;
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

        return $this->data;
    }
}
