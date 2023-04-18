<?php

namespace Adminx\Common\Models;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\MenuItemConfig;
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
        'config'    => MenuItemConfig::class,
    ];

    protected $appends = [
        'text',
        //'uri',
    ];

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'                     => ['required'],
            'external'                  => ['boolean', 'nullable'],
            'menuable_type'             => ['required'],
            'menuable_type_page_id'     => [
                Rule::requiredIf(collect([
                                             'page',
                                             'post',
                                             'category',
                                         ])->contains($request->menuable_type ?? '') !== false),
            ],
            'menuable_type_post_id'     => [Rule::requiredIf(($request->menuable_type ?? '') === 'post')],
            'menuable_type_category_id' => [Rule::requiredIf(($request->menuable_type ?? '') === 'category')],
            'url'                       => [Rule::requiredIf(($request->menuable_type ?? null) === 'link')],
        ];
    }

    public static function createMessages(): array
    {
        return [
            'menuable_type.required'             => 'Selecione o tipo de Item.',
            'menuable_type_page_id.required'     => 'Selecione uma página.',
            'menuable_type_post_id.required'     => 'Selecione um Post.',
            'menuable_type_category_id.required' => 'Selecione uma Categoria.',
            'url.required'                       => 'Insira um Link para seu Item.',
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
        return Str::contains($this->menuable_type, ['page', 'post', 'category']) && $this->menuable_id;
    }

    public function jsonRelations(): static
    {
        if ($this->hasMenuable()) {
            $this->load(['menuable', 'children', 'parent'/*, 'page', 'post', 'category'*/]);
        }

        return $this;
    }

    public function mount(SpatieMenu $menuBuilder): SpatieMenu
    {
        if ($this->menuable_type === 'menu' || $this->children->count()) {

            /*$subMenu = SpatieMenu::new()
                                 ->addClass($this->menu->config->submenu_class ?? '');*/

            $menuBuilder->submenu(
                Link::to('#', $this->title)
                    ->setAttributes([
                                        'data-toggle' => 'dropdown',
                                        'role'        => 'button',
                                    ]),
                function (SpatieMenu $subMenu) {

                    $subMenu->addClass($this->menu->config->submenu_class ?? '');

                    if($this->config->is_source_submenu && $this->config->submenu_source->data->id ?? false){
                        //Subitens de uma fonte de dados
                        $sourceData = $this->config->submenu_source->data->mountModel();


                        if($sourceData->items->count() ?? false){
                            foreach($sourceData->items as $dataItem){
                                $subMenu->add(Link::to($dataItem->url, $dataItem->title)->addParentClass($this->menu->config->menu_item_class ?? ''));
                            }
                        }

                    }else if($this->children->count()){
                        //Subitens cadastrados
                        foreach ($this->children as $childMenuItem) {
                            $subMenu = $childMenuItem->mount($subMenu);
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
            $menuBuilder->add(Link::to($this->url, $this->title)->addParentClass($this->menu->config->menu_item_class ?? ''));
        }

        return $menuBuilder;
    }

    public function loadUrlFromMenuable(): static
    {

        if ($this->menuable) {
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
        /*if (collect(['page', 'post', 'category'])->contains($this->menuable_type)) {
            if ($this->model && $this->model->url ?? false) {
                return $this->model->url;
            }
        }*/

        if (empty($this->attributes['url'])) {
            return null;
        }

        return (Str::of($this->attributes['url'])->startsWith('/') ? '' : '/') . $this->attributes['url'];
    }

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

    public function post()
    {
        return $this->morphedByMany(Post::class, 'menuable');
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
