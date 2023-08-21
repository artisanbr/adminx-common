<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\CustomLists\Abstract\CustomListBase;
use Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Repositories\Base\Repository;
use Exception;

/**
 * @property ?CustomListItemBase $model
 */
class CustomListItemRepository extends Repository
{
    public ?int           $list_id;
    public CustomListBase $customList;
    protected string $modelClass = CustomListItem::class;

    /*public function __construct(
        protected CustomListItemBase|null $listItem = null,
    ) {}*/

    public function customList($list_id): static
    {
        $this->list_id = $list_id;

        $this->customList = CustomList::findAndMount($list_id);

        return $this;
    }


    public function saveTransaction(): ?CustomListItemBase
    {
        $this->setModel($this->customList->items()->findOrNew($this->data['id'] ?? null));

        $this->model->fill($this->data);
        $this->model->list_id = $this->list_id;

        if (!$this->model->id) {
            $this->model->newPosition();
        }

        $this->model->save();
        $this->model->refresh();

        $this->processUploads();

        $this->model->save();

        return $this->model;
    }

    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->model || !$this->model->site) {
            abort(404);
        }

        $this->model->refresh();

        //$this->uploadPathBase = "lists/{$this->model->list->public_id}/items";

        $this->uploadPathBase = $this->model->uploadPathTo();

        //Media
        if ($this->data['data']['image']['file_upload'] ?? false) {

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['data']['image']['file_upload'], $this->uploadPathBase, $this->model->public_id, $this->model->data->image->file ?? null);

            $mediaFile = FileUpload::upload($this->data['data']['image']['file_upload'], $this->uploadPathBase, $this->model->public_id);

            if (!$mediaFile) {
                abort(500);
            }

            $this->model->data->image_url = $mediaFile->url;
        }


        //Todo: Assets
    }

    /**
     * @param array{id: string, order: string, parentId?: string} $items
     *
     * @return bool
     */
    public function updateList(array $items): bool
    {
        $retorno = true;
        foreach ($items as $i => $item) {
            $listItem = CustomListItem::findAndMount($item);

            if ($listItem) {
                $listItem->position = $i;

                $retorno = $listItem->save();
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
