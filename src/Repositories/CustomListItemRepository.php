<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Facades\FileManager\FileManager;
use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItem;
use Adminx\Common\Models\CustomLists\Object\Schemas\CustomListItemSchemaValue;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

/**
 * @property ?CustomListItem $model
 */
class CustomListItemRepository extends Repository
{
    public ?int       $list_id;
    public CustomList $customList;
    protected string  $modelClass = CustomListItem::class;

    /*public function __construct(
        protected CustomListItem|null $listItem = null,
    ) {}*/

    public function customList($list_id): static
    {
        $this->list_id = $list_id;

        $this->customList = CustomList::find($list_id);

        return $this;
    }


    public function saveTransaction(): ?CustomListItem
    {
        $this->setModel($this->customList->items()->findOrNew($this->data['id'] ?? null));

        $sendSchema = collect($this->data['schema'] ?? []);

        $sendData = collect($this->data)->forget('schema')->toArray();

        $this->model->fill($sendData);
        $this->model->list_id = $this->list_id;

        if (!$this->model->id) {
            $this->model->newPosition();
        }


        $this->uploadPathBase = $this->model->uploadPathTo();

        $newSchema = tap(collect(), function (Collection $schema) use ($sendSchema) {

            foreach ($this->customList->schema as $schemaColumn) {

                $sendDataForSchema = $sendSchema->firstWhere('slug', $schemaColumn->slug);

                $sendValueForSchema = $sendDataForSchema['value'] ?? null;

                $fillData = [
                    'name'   => $schemaColumn->name,
                    'slug'   => $schemaColumn->slug,
                    'type'   => $schemaColumn->type->value,
                    'column' => $schemaColumn,
                    'value'  => $sendValueForSchema,
                ];

                $dataItem = $this->model
                    ->getSchemaValueForColumn($schemaColumn)
                    //Sincronizar com os dados atuais da Coluna
                    ->fill($fillData);

                if ($schemaColumn->type->is(CustomListSchemaType::Image)) {

                    $uploadedFile = $sendValueForSchema['uploaded_file'] ?? false;
                    $renameFileTo = $sendValueForSchema['rename_to'] ?? null;


                    //Arquivo enviado
                    if ($uploadedFile instanceof UploadedFile) {



                        $mediaFile = FileUpload::upload($uploadedFile, $this->uploadPathBase, $renameFileTo ?: $uploadedFile->getClientOriginalName());

                        if (!$mediaFile) {
                            abort(500);
                        }

                        //$this->model->data->image_url = $mediaFile->url;

                        $dataItem->value->fill([
                                                   'external'      => false,
                                                   'url'           => $mediaFile->url,
                                                   'uploaded_file' => null,
                                               ]);

                        /*$transformValue = function (CustomListItemSchemaValue $item, $key) use ($mediaFile) {
                            $item->value = $item->value->fill([
                                                                  'external' => false,
                                                                  'url'      => $mediaFile->url,
                                                              ]);

                            return $item;
                        };

                        $saveFile = $this->model->transformSchemaBySlug($data, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                        if ($saveFile) {
                            $this->model->save();
                        }*/
                    }
                    //Arquivo para renomear
                    if (!blank($renameFileTo)) {

                        /*$dataItem = $this->model->schema->firstWhere('slug', $sendDataForSchema['slug']);*/


                        $renameResult = FileManager::rename($dataItem->value->path, $renameFileTo);

                        if ($renameResult) {

                            $dataItem->value->fill([
                                                       'external'  => false,
                                                       'url'       => $renameResult,
                                                       'rename_to' => null,
                                                   ]);

                            /*$transformValue = function (CustomListItemSchemaValue $item, $key) use ($renameResult) {
                                $item->value = $item->value->fill([
                                                                      'external'  => false,
                                                                      'url'       => $renameResult,
                                                                      'rename_to' => null,
                                                                  ]);

                                return $item;
                            };

                            $saveFile = $this->model->transformSchemaBySlug($valueRef, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                            if ($saveFile) {
                                $this->model->save();
                            }*/

                        }


                        //FileManager::rename()

                    }
                }

                if ($schemaColumn->type->is(CustomListSchemaType::SEO)) {

                    $uploadedFile = $sendValueForSchema['image_file'] ?? false;


                    //Arquivo enviado
                    if ($uploadedFile instanceof UploadedFile) {


                        $mediaFile = FileUpload::upload($uploadedFile, $this->uploadPathBase, $uploadedFile->getClientOriginalName());

                        if (!$mediaFile) {
                            abort(500);
                        }

                        //$this->model->data->image_url = $mediaFile->url;

                        $dataItem->value->fill([
                                                   'image_url' => $mediaFile->url,
                                               ]);

                        /*$transformValue = function (CustomListItemSchemaValue $item, $key) use ($mediaFile) {
                            $item->value = $item->value->fill([
                                                                  'external' => false,
                                                                  'url'      => $mediaFile->url,
                                                              ]);

                            return $item;
                        };

                        $saveFile = $this->model->transformSchemaBySlug($data, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                        if ($saveFile) {
                            $this->model->save();
                        }*/
                    }
                    //Arquivo para renomear
                    if (!blank($renameFileTo)) {

                        /*$dataItem = $this->model->schema->firstWhere('slug', $sendDataForSchema['slug']);*/


                        $renameResult = FileManager::rename($dataItem->value->path, $renameFileTo);

                        if ($renameResult) {

                            $dataItem->value->fill([
                                                       'external'  => false,
                                                       'url'       => $renameResult,
                                                       'rename_to' => null,
                                                   ]);

                            /*$transformValue = function (CustomListItemSchemaValue $item, $key) use ($renameResult) {
                                $item->value = $item->value->fill([
                                                                      'external'  => false,
                                                                      'url'       => $renameResult,
                                                                      'rename_to' => null,
                                                                  ]);

                                return $item;
                            };

                            $saveFile = $this->model->transformSchemaBySlug($valueRef, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                            if ($saveFile) {
                                $this->model->save();
                            }*/

                        }


                        //FileManager::rename()

                    }
                }

                $schema->push($dataItem);
            }

            //return $schema;

        });

        $this->model->setAttribute('schema', $newSchema);

        /*foreach ($sendSchema as $sendValueIndex => $sendValueItem) {

            //todo: Gerar lista completa do schema
            //todo: testar replace do collection nas models genericas

            $valueRef = $sendValueItem['slug'] ?? $sendValueItem['column']['id'] ?? null;

            if (empty($valueRef)) {
                continue;
            }

            //Tratar uploads de acordo com cada tipo de coluna
            switch ($sendValueItem['type']) {
                case CustomListSchemaType::Image->value:
                    $uploadedFile = $sendValueItem['value']['uploaded_file'] ?? false;
                    $renameFileTo = $sendValueItem['value']['rename_to'] ?? null;


                    //Arquivo enviado
                    if ($uploadedFile instanceof UploadedFile) {


                        $mediaFile = FileUpload::upload($uploadedFile, $this->uploadPathBase, $renameFileTo ?: $uploadedFile->getClientOriginalName());

                        if (!$mediaFile) {
                            abort(500);
                        }

                        //$this->model->data->image_url = $mediaFile->url;

                        $transformValue = function (CustomListItemSchemaValue $item, $key) use ($mediaFile) {
                            $item->value = $item->value->fill([
                                                                  'external' => false,
                                                                  'url'      => $mediaFile->url,
                                                              ]);

                            return $item;
                        };

                        $saveFile = $this->model->transformSchemaBySlug($valueRef, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                        if ($saveFile) {
                            $this->model->save();
                        }
                    }
                    //Arquivo para renomear
                    if (!blank($renameFileTo)) {

                        $dataItem = $this->model->schema->firstWhere('slug', $sendValueItem['slug']);


                        $renameResult = FileManager::rename($dataItem->value->path, $renameFileTo);

                        if ($renameResult) {


                            $transformValue = function (CustomListItemSchemaValue $item, $key) use ($renameResult) {
                                $item->value = $item->value->fill([
                                                                      'external'  => false,
                                                                      'url'       => $renameResult,
                                                                      'rename_to' => null,
                                                                  ]);

                                return $item;
                            };

                            $saveFile = $this->model->transformSchemaBySlug($valueRef, $transformValue) || $this->model->transformSchemaByColumnId($valueRef, $transformValue);

                            if ($saveFile) {
                                $this->model->save();
                            }

                        }


                        //FileManager::rename()

                    }

                    break;
                default:
                    break;
            }
        }*/

        $this->model->save();
        //$this->model->refresh();

        //$this->processUploads();

        //$this->model->save();

        //Categories
        if ($this->data['categories'] ?? false) {
            $coreListItem = CustomListItem::find($this->model->id);

            $coreListItem->categoriesMorph()->sync($this->data['categories']);
        }


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

        //dd($this->model->data->toArray(), $this->data);

        $this->uploadPathBase = $this->model->uploadPathTo();

        if (isset($this->data['schema']) && is_array($this->data['schema'])) {
            foreach ($this->data['schema'] as $sendDataIndex => $sendDataItem) {
                $dataRef = $sendDataItem['slug'] ?: $sendDataItem['column']['id'] ?? null;

                if (empty($dataRef)) {
                    continue;
                }

                //Tratar uploads de acordo com cada tipo de coluna
                switch ($sendDataItem['type']) {
                    case CustomListSchemaType::Image->value:
                        $uploadedFile = $sendDataItem['value']['uploaded_file'] ?? false;
                        $renameFileTo = $sendDataItem['value']['rename_to'] ?? null;


                        //Arquivo enviado
                        if ($uploadedFile instanceof UploadedFile) {


                            $mediaFile = FileUpload::upload($uploadedFile, $this->uploadPathBase, $renameFileTo ?: $uploadedFile->getClientOriginalName());

                            if (!$mediaFile) {
                                abort(500);
                            }

                            //$this->model->data->image_url = $mediaFile->url;

                            $transformValue = function (CustomListItemSchemaValue $item, $key) use ($mediaFile) {
                                $item->value = $item->value->fill([
                                                                      'external' => false,
                                                                      'url'      => $mediaFile->url,
                                                                  ]);

                                return $item;
                            };

                            $saveFile = $this->model->transformSchemaBySlug($dataRef, $transformValue) || $this->model->transformSchemaByColumnId($dataRef, $transformValue);

                            if ($saveFile) {
                                $this->model->save();
                            }
                        }
                        //Arquivo para renomear
                        if (!blank($renameFileTo)) {

                            $dataItem = $this->model->schema->firstWhere('slug', $sendDataItem['slug']);


                            $renameResult = FileManager::rename($dataItem->value->path, $renameFileTo);

                            if ($renameResult) {


                                $transformValue = function (CustomListItemSchemaValue $item, $key) use ($renameResult) {
                                    $item->value = $item->value->fill([
                                                                          'external'  => false,
                                                                          'url'       => $renameResult,
                                                                          'rename_to' => null,
                                                                      ]);

                                    return $item;
                                };

                                $saveFile = $this->model->transformSchemaBySlug($dataRef, $transformValue) || $this->model->transformSchemaByColumnId($dataRef, $transformValue);

                                if ($saveFile) {
                                    $this->model->save();
                                }

                            }


                            //FileManager::rename()

                        }

                        break;
                    default:
                        break;
                }
            }
        }


        //$this->uploadPathBase = "lists/{$this->model->list->public_id}/items";


        //Media
        /*if ($this->data['data']['image']['file_upload'] ?? false) {

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['data']['image']['file_upload'], $this->uploadPathBase, $this->model->public_id, $this->model->data->image->file ?? null);

            $mediaFile = FileUpload::upload($this->data['data']['image']['file_upload'], $this->uploadPathBase, $this->model->public_id);

            if (!$mediaFile) {
                abort(500);
            }

            $this->model->data->image_url = $mediaFile->url;
        }*/


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
            $listItem = CustomListItem::find($item);

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