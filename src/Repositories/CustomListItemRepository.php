<?php

namespace Adminx\Common\Repositories;

use App\Repositories\Exception;
use Adminx\Common\Enums\FileType;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\Bases\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Repositories\Base\Repository;
use Illuminate\Support\Facades\DB;

class CustomListItemRepository extends Repository
{
    public int|null $list_id;
    public CustomListBase $customList;

    public function __construct(
        protected CustomListItemBase|null $listItem = null,
    ) {}

    public function customList($list_id): static
    {
        $this->list_id = $list_id;

        $this->customList = CustomList::findAndMount($list_id);

        return $this;
    }


    public function save(array $data): ?CustomListItemBase
    {
        $this->traitData($data);


        return DB::transaction(function(){

            $this->listItem = $this->customList->items()->findOrNew($this->data['id'] ?? null);

            $this->listItem->fill($this->data);
            $this->listItem->list_id = $this->list_id;

            if(!$this->listItem->id){
                $this->listItem->newPosition();
            }

            $this->listItem->save();
            $this->listItem->refresh();

            $this->processUploads();

            $this->listItem->save();

            return $this->listItem;
        });
    }

    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->listItem || !$this->listItem->site) {
            abort(404);
        }

        $this->listItem->refresh();

        $this->uploadPathBase = "lists/{$this->listItem->list->public_id}/items";
        $this->uploadableType = MorphHelper::resolveMorphType($this->listItem);

        //Media

        if($this->data['data']['image']['file_upload'] ?? false){

            $mediaFile = FileHelper::saveRequestToSite($this->listItem->site, $this->data['data']['image']['file_upload'], $this->uploadPathBase, $this->listItem->public_id, $this->listItem->data->image->file ?? null);

            if(!$mediaFile){
                abort(500);
            }

            $mediaFile->fill([
                                 'title'           => "Item de Lista #{$this->listItem->public_id}",
                                 'type'           => FileType::CustomListItem,
                                 'description'     => "Item #{$this->listItem->public_id} da lista #{$this->listItem->list->public_id}",
                                 'editable'     => false,
                             ]);

            $mediaFile->assignTo($this->listItem, 'uploadable');

            $this->listItem->data->image->file_id = $mediaFile->id;
        }


        //Todo: Assets
    }

    /**
     * @param array{id: string, order: string, parentId?: string} $items
     *
     * @return bool
     */
    public function updateList(array $items): bool{
        $retorno = true;
        foreach ($items as $i => $item){
            $listItem = CustomListItem::findAndMount($item);

            if($listItem) {
                $listItem->position = $i;

                $retorno = $listItem->save();
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
