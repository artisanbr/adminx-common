<?php

namespace Adminx\Common\Models\Pages\Modules\Manager;


use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Illuminate\Support\Collection;

class PageModuleManagerEngine
{
    /**
     * @var Collection|AbstractPageModule[]
     */
    protected Collection|array $items;
    protected string           $configName = 'common.models.page.modules';

    public function __construct()
    {
        $this->classMap = collect(config($this->configName));

        $this->items = $this->classMap->map(static fn($typeClass, $typeName) => new $typeClass());
    }

    public function getModuleClass($type): string
    {
        return config("{$this->configName}.{$type}");
    }

    public function getModule($module)
    {
        return $this->items->get($module);
    }

    /**
     * @return AbstractPageModule[]|Collection
     */
    public function modules(): array|Collection
    {
        return $this->items;
    }
}