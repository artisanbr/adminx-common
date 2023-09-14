<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Illuminate\Foundation\Http\FormRequest;

class CustomList extends CustomListAbstract
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
