<?php

use ArtisanBR\Adminx\Common\App\Enums\CustomLists\CustomListType;

return [
    'types' => [
        //Wdigets
        'widget' => array_merge([
                                     'page'          => [
                                         'title'       => 'Página Individual',
                                         'description' => 'Possível vincular uma página como fonte de dados.',
                                     ],
                                     'posts'    => [
                                         'title'       => 'Postagens',
                                         'description' => 'Possível vincular as postagens de uma página como fonte de dados.',
                                         'sorting_columns' => [
                                             'published_at' => 'Data de Publicação',
                                             'title' => 'Título da Postagem',
                                             'created_at' => 'Data de Criação',
                                             'updated_at' => 'Data da Ultima Atualização',
                                         ],
                                     ],
                                     'products' => [
                                         'title'       => 'Produtos',
                                         'description' => 'Possível vincular os produtos de uma página como fonte de dados.',
                                     ],
                                     'post'          => [
                                         'title'       => 'Postagem Individual',
                                         'description' => 'Possível vincular uma única postagem de qualquer página como fonte de dados.',
                                     ],
                                     'address'       => [
                                         'title'       => 'Endereço/Localização',
                                         'description' => 'Possível vincular um dos endereços de contato cadastrados no site.',
                                     ],
                                     'form'          => [
                                         'title'       => 'Formulário',
                                         'description' => 'Possível vincular um dos formulários cadastrados no site.',
                                     ],
                                     'list'          => [
                                         'title'       => 'Lista Customizada',
                                         'description' => 'Possível vincular uma lista como fonte de dados.',
                                         'sorting_columns' => [
                                             'position' => 'Ordenação definida manualmente',
                                             'title' => 'Título da Postagem',
                                             'created_at' => 'Data de Criação',
                                             'updated_at' => 'Data da Ultima Atualização',
                                         ],
                                     ],
                                 ],
                                 collect(CustomListType::titles())->mapWithKeys(fn($title, $value) => [
                                     "list.{$value}" => [
                                         'title'       => "Lista: {$title}",
                                         'description' => "Possível vincular uma lista do tipo \"{$title}\" como fonte de dados.",
                                         'sorting_columns' => [
                                             'position' => 'Ordenação definida manualmente',
                                             'title' => 'Título da Postagem',
                                             'created_at' => 'Data de Criação',
                                             'updated_at' => 'Data da Ultima Atualização',
                                         ],
                                     ],
                                 ])->toArray()),
        //Pages
        'page' => array_merge([
                                   'list' => [
                                       'title'       => 'Lista Customizada',
                                       'description' => 'Possível vincular uma lista como fonte de dados.',
                                       'sorting_columns' => [
                                           'position' => 'Ordenação definida manualmente',
                                           'title' => 'Título da Postagem',
                                           'created_at' => 'Data de Criação',
                                           'updated_at' => 'Data da Ultima Atualização',
                                       ],
                                   ],

                               ],
                               collect(CustomListType::titles())->mapWithKeys(fn($title, $value) => [
                                   "list.{$value}" => [
                                       'title'       => "Lista: {$title}",
                                       'description' => "Possível vincular uma lista do tipo \"{$title}\" como fonte de dados.",
                                   ],
                               ])->toArray()),
        //Menus
        'menu_item' => array_merge([
                                   'list' => [
                                       'title'       => 'Lista Customizada',
                                       'description' => 'Possível vincular uma lista como fonte de dados.',
                                   ],

                               ],
                               collect(CustomListType::titles())->mapWithKeys(fn($title, $value) => [
                                   "list.{$value}" => [
                                       'title'       => "Lista: {$title}",
                                       'description' => "Possível vincular uma lista do tipo \"{$title}\" como fonte de dados.",
                                   ],
                               ])->toArray()),
    ],

];
