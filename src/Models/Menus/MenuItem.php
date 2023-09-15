<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus;

use Adminx\Common\Enums\MenuItemType;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Menus\Objects\MenuItemConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu as SpatieMenu;

class MenuItem extends EloquentModelBase
{
    use HasSelect2, HasUriAttributes, HasValidation, HasMorphAssigns, HasParent;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'menuable_id',
        'menuable_type',
        'title',
        'type',
        'external',
        'url',
        'position',
        'order',
        'config',
    ];

    protected $attributes = [
        'external' => 0,
    ];

    protected $casts = [
        'title'    => 'string',
        'external' => 'boolean',
        'position' => 'integer',
        'order'    => 'integer',
        'type'     => MenuItemType::class,
        'config'   => MenuItemConfig::class,
    ];

    protected $appends = [
        'text',
        //'uri',
    ];

    protected $touches = ['menu'];

    //protected $with = ['children'];

    //region VALIDATIONS
    public static function createRules(?FormRequest $request = null): array
    {
        return [
            'title'                          => ['required'],
            'external'                       => ['boolean', 'nullable'],
            'menuable_type'                  => ['nullable'],
            'menuable_type_page_id'          => [
                Rule::requiredIf($request?->type !== 'link' && collect([
                                                                           'page',
                                                                           'article',
                                                                           'category',
                                                                           'page_internal',
                                                                       ])->contains($request?->menuable_type ?? '') !== false),
            ],
            'menuable_type_article_id'       => [Rule::requiredIf(($request?->menuable_type ?? '') === 'article')],
            'menuable_type_category_id'      => [Rule::requiredIf(($request?->menuable_type ?? '') === 'category')],
            'menuable_type_page_internal_id' => [Rule::requiredIf(($request?->menuable_type ?? '') === 'page_internal')],
            'url'                            => [Rule::requiredIf(($request?->menuable_type ?? null) === 'link')],
        ];
    }

    public static function createMessages(): array
    {
        return [
            'menuable_type.required'                  => 'Selecione o tipo de Item.',
            'menuable_type_page_id.required'          => 'Selecione uma página.',
            'menuable_type_article_id.required'       => 'Selecione um Post.',
            'menuable_type_category_id.required'      => 'Selecione uma Categoria.',
            'menuable_type_page_internal_id.required' => 'Selecione uma Página Interna.',
            'url.required'                            => 'Insira um Link para seu Item.',
        ];
    }
    //endregion

    //region HELPERS
    public function newPosition(): void
    {
        if (is_null($this->position) || !$this->id) {
            $this->load(['menu']);
            $this->position = $this->menu->items()->where('parent_id', $this->parent_id)->count();
        }
    }

    public function hasMenuable(): bool
    {
        return Str::contains($this->menuable_type, ['page', 'article', 'category']) && $this->menuable_id;
    }

    public function jsonRelations(): static
    {
        if ($this->hasMenuable()) {
            $this->load(['menuable', 'children', 'parent'/*, 'page', 'article', 'category'*/]);
        }

        return $this;
    }

    public function mount(SpatieMenu $menuBuilder, ?Menu $menu = null): SpatieMenu
    {
        if (!$menu) {
            $menu = $this->menu;
        }

        if ($this->type === MenuItemType::Submenu) {

            /*$subMenu = SpatieMenu::new()
                                 ->addClass($this->menu->config->submenu_class ?? '');*/

            $useUrl = ($this->config->use_submenu_url && $this->url);

            $itemLink = Link::to($useUrl ? $this->url : '#', $this->title)
                            ->setAttributes([
                                                'data-toggle' => 'dropdown',
                                                ...(!$useUrl ? [
                                                    'role' => 'button',
                                                ] : []),
                                            ]);

            if (!$this->parent_id) {
                $itemLink->addClass($menu->config->render->parent_item_link->class ?? '');
                $itemLink->addParentClass($menu->config->render->parent_item->class ?? '');
            }

            $menuBuilder->submenu(
                $itemLink,
                function (SpatieMenu $subMenu) use ($menu) {

                    $subMenu->addClass($menu->config->render->submenu->class ?? '');

                    if ($this->menuable_type === 'page_internal' && $this->menuable && $this->menuable->model && method_exists($this->menuable->model, 'items')) {

                        //Debugbar::debug($this->menuable->model);

                        $customList = $this->menuable->model;

                        if (method_exists($customList, 'mountModel')) {
                            $customList = $customList->mountModel();
                        }


                        foreach ($customList->items as $modelItem) {
                            $subMenu->add(Link::to($modelItem->url, $modelItem->title)->addParentClass($menu->config->render->item->class ?? ''));
                        }

                    }/*else if ($this->config->is_source_submenu && $this->config->submenu_source->data->id ?? false) {
                        //Subitens de uma fonte de dados
                        $sourceData = $this->config->submenu_source->data->mountModel();


                        foreach ($sourceData->items as $dataItem) {
                            $subMenu->add(Link::to($sourceData->itemUrl($dataItem), $dataItem->title)->addParentClass($menu->config->menu_item_class ?? ''));
                        }

                    }*/
                    else if ($this->children->count()) {
                        //Subitens cadastrados
                        foreach ($this->children as $childMenuItem) {
                            $subMenu = $childMenuItem->mount($subMenu, $menu);
                        }
                    }
                }
            );


            /* $menuBuilder->add($this->title, function (SpatieMenu $submenuBuilder) {

                 foreach ($this->children->all() as $children) {
                     $submenuBuilder->add($children->title, function (SpatieMenu $submenuBuilder, self $childItem) {
                         $childItem->mount($submenuBuilder);
                     }))
                     //$submenuBuilder->add(Link::to($children->url, $children->title));

                     $submenuBuilder = $children->mount($submenuBuilder);

                 }

                 return $submenuBuilder;

             });*/

        }
        else {
            $itemLink = Link::to($this->url, $this->title)->addParentClass($menu->config->render->item->class ?? '');

            if (!$this->parent_id) {
                $itemLink->addClass($menu->config->render->parent_item_link->class ?? '');
                $itemLink->addParentClass($menu->config->render->parent_item->class ?? '');
            }

            $menuBuilder->add($itemLink);
        }

        return $menuBuilder;
    }

    public function loadUrlFromMenuable(): static
    {

        if ($this->menuable_type && $this->menuable_id && $this->menuable) {
            $this->attributes['url'] = $this->menuable->url ?? null;
        }

        return $this;
    }
    //endregion

    //region ATTRIBUTES
    protected function order(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->position,
            set: function ($value) {
                $this->position = $value;
            },
        );
    }

    protected function external(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ?? false,
        );
    }

    //region GETS
    protected function getUrlAttribute()
    {
        /*if (collect(['page', 'article', 'category'])->contains($this->menuable_type)) {
            if ($this->model && $this->model->url ?? false) {
                return $this->model->url;
            }
        }*/

        if (empty($this->attributes['url'])) {
            return null;
        }

        return (Str::of($this->attributes['url'])->startsWith('/') ? '' : '/') . $this->attributes['url'];
    }
    //endregion

    //endregion

    //endregion OVERRIDES
    public function save(array $options = [])
    {
        $this->loadUrlFromMenuable();

        return parent::save($options); // TODO: Change the autogenerated stub
    }

    //endregion

    //region SCOPES
    public function scopeMount(Builder $query, $parent_id = null)
    {
        return $query->where('parent_id', $parent_id)->with(['children'])->orderBy('position');
    }
    //endregion

    //region RELATIONS
    public function menuable()
    {
        return $this->morphTo();
    }

    public function page()
    {
        return $this->morphedByMany(Page::class, 'menuable');
    }

    public function article()
    {
        return $this->morphedByMany(Article::class, 'menuable');
    }

    public function category()
    {
        return $this->morphedByMany(Category::class, 'menuable');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(__CLASS__);
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id')->with('children')->orderBy('position');
    }
    //endregion
}
