<?php

namespace Adminx\Common\Models\Templates\Global\Manager;


use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;
use Adminx\Common\Models\Templates\Global\Abstract\AbstractPageTemplate;
use Illuminate\Support\Collection;

class PageTemplateManagerEngine
{
    /**
     * @var Collection|AbstractPageModule[]
     */
    protected Collection|array $items;
    /**
     * @var Collection|string[]
     */
    protected Collection|array $classMap;
    protected string           $configName = 'common.models.page.templates';

    public function __construct()
    {
        $this->classMap = collect(config($this->configName));

        $this->items = $this->classMap->map(static fn($typeClass, $typeName) => new $typeClass());
    }

    public function getTemplateClass($type): string
    {
        return config("{$this->configName}.{$type}");
    }

    public function getTemplate($type)
    {
        return $this->items->get($type);
    }

    public function selectOptions()
    {
        return $this->items->map(static fn($type) => $type->text)->toArray();
    }

    /**
     * @return AbstractPageTemplate[]|Collection
     */
    public function templates(): array|Collection
    {
        return $this->items;
    }

    public function globalTemplatesPath($path = '')
    {
        return base_path("vendor/artisanbr/adminx-common/resources/templates/{$path}");
    }
}