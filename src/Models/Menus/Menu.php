<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Menus\Objects\MenuConfig;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Users\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Menu\Laravel\Menu as SpatieMenu;
use Spatie\Menu\Link;

class Menu extends EloquentModelBase implements OwneredModel
{
    use HasUriAttributes,
        HasValidation,
        BelongsToSite,
        BelongsToUser,
        BelongsToAccount,
        HasOwners;

    protected $fillable = [
        'title',
        'slug',
        'site_id',
        'account_id',
        'user_id',
        'config',
        'html',
    ];

    protected $casts = [
        'title'  => 'string',
        'slug'   => 'string',
        'config' => MenuConfig::class,
    ];

    //protected $with = ['site'];

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title' => ['required'],
            'slug'  => [
                'required',
                Rule::unique('menus')->ignore($request->id)->where(function (Builder $query) use ($request) {
                    return $query->where('site_id', Auth::user()->site_id);
                }),
            ],
        ];
    }

    public static function createMessages(FormRequest $request = null): array
    {
        return [
            'title.required' => 'O título do menu é obrigatório',
            'slug.required'  => 'O apelido do menu é obrigatório',
            'slug.unique'    => 'O apelido do menu deve ser único entre os menus do site.',
        ];
    }
    //endregion

    //region HELPERS
    public function mount(callable $callback = null): SpatieMenu|string
    {

        $menuBuilder = SpatieMenu::new()->addClass($this->config->render->menu->class ?? '');

        $menuParentItems = $this->parent_items;


        foreach ($menuParentItems as $menuItem) {
            $menuBuilder = $menuItem->mount($menuBuilder, $this);
        }

        return $menuBuilder->each(function (SpatieMenu $submenu) {
            $submenu->addParentClass($this->config->render->item_submenu->class ?? '');

            $submenu->addParentClass($this->config->render->item->class ?? '');

            $submenu->each(function (Link $link) {
                /*$link->prepend($this->config->menu_item_prepend ?? '');

                $link->append($this->config->menu_item_append ?? '');*/

                $link->addParentClass($this->config->render->submenu_item->class ?? '');

                $link->addClass($this->config->render->submenu_item_link->class ?? '');
            });

        })->each(function (Link $link) {
            $link->prepend($this->config->menu_item_prepend ?? '');
            $link->append($this->config->menu_item_append ?? '');

            $link->addParentClass($this->config->render->item->class ?? '');

            $link->addClass($this->config->render->item_link->class ?? '');

        });;


    }
    //endregion

    //region ATTRIBUTES
    public function mount_html()
    {
        $submenuItensWithLink = $this->items()->where('config->use_submenu_url', true)->count();

        return $this->mount()->render() . ($submenuItensWithLink ? <<<html
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    $('ul > li > a[href][data-toggle]').click(function(e) {
        var dropdown = $(this).next('.dropdown-menu');
    
        if (dropdown.length == 0 || dropdown.css('display') !== 'none') {
        
        console.log(this.href);
            if (this.href && this.href != '#') {
                e.preventDefault();
                location.href = this.href;
                
                return false;
            }
        }
    });
});
</script>
html: '');
    }

    /*public function html(): Attribute
    {
        return Attribute::make(get: fn() => $this->mount_html());
    }*/

    protected function slug(): Attribute
    {
        return Attribute::make(set: static fn($value) => Str::slug(Str::replaceNative([
                                                                                          'menu-',
                                                                                          'menu ',
                                                                                          'menu',
                                                                                      ], '', Str::lower($value))));
    }
    //endregion

    //region OVERRIDES
    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

    public function save(array $options = [])
    {
        //Apelido
        if (empty($this->slug)) {
            $this->slug = $this->title;
        }

        return parent::save($options); // TODO: Change the autogenerated stub
    }
    //endregion

    //region RELATIONS
    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'id')->orderBy('parent_id')->orderBy('position');
    }

    public function parent_items()
    {
        return $this->items()->where('parent_id', null)->orderBy('position');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    //endregion
}
