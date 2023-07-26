<?php

namespace Adminx\Common\Models\Pages\Types\Manager;

use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;
use Illuminate\Support\Collection;

class PageTypeManagerEngine
{

    protected Collection $classMap;

    /**
     * @var Collection|AbstractPageModule[]
     */
    protected Collection|array $items;

    protected string $configName = 'common.models.page.types';

    public function __construct()
    {
        $this->classMap = collect(config($this->configName));

        $this->items = $this->classMap->map(static fn($typeClass, $typeName) => new $typeClass());;
    }


    public function whereCanUseModule(string $module): Collection
    {
        return $this->items->filter(static fn(AbstractPageType $type) => $type->canUseModule($module));
    }

    public function whereCanUseAnyModule(string|array $modules): Collection
    {
        return $this->items->filter(static fn(AbstractPageType $type) => $type->canUseAnyModule($modules));
    }

    public function whereCanUseAllModule(string|array $modules): Collection
    {
        return $this->items->filter(static fn(AbstractPageType $type) => $type->canUseAllModules($modules));
    }

    public function getTypeClass($type): string
    {
        return config("{$this->configName}.{$type}");
    }

    public function getType($type)
    {
        return $this->items->get($type);
    }

    public function selectOptions()
    {
        return $this->items->map(static fn($type) => $type->text)->toArray();
    }

    public function types()
    {
        return $this->items;
    }


}