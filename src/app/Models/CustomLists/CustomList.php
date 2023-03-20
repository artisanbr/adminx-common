<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists;

use ArtisanBR\Adminx\Common\App\Models\Bases\CustomListBase;
use Illuminate\Foundation\Http\FormRequest;

class CustomList extends CustomListBase
{

    /*protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'type' => CustomListType::class,
        'config' => 'object',
    ];*/


    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title' => ['required'],
            'type'  => ['required'],
        ];
    }

    public static function createMessages(FormRequest $request = null): array
    {
        return [
            'title.required' => 'O título da lista é obrigatório',
            'type.required'  => 'O tipo de lista é obrigatório',
        ];
    }
    //endregion

}
