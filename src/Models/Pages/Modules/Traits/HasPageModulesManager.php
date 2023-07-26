<?php

namespace Adminx\Common\Models\Pages\Modules\Traits;

use Adminx\Common\Models\Pages\Objects\PageConfig;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;

/**
 * @property Collection|string[] $allowed_modules
 * @property Collection|string[] $enabled_modules
 * @property ?PageConfig $config
 */
trait HasPageModulesManager
{
    //region Can's
    protected function getIsUsingArticlesModuleAttribute(): bool
    {
        return $this->isUsingModule('articles');
    }

    protected function getCanUseArticlesModuleAttribute(): bool
    {
        return $this->canUseModule('articles');
    }

    //todo: list, forms
    //endregion

    //region Modules Management

    public function canUseAllModules(array|string $modules): bool
    {

        foreach (Collection::wrap($modules)->toArray() as $module){
            if(!$this->canUseModule($module)){
                return true;
            }
        }

        return true;
    }

    public function canUseAnyModule(array|string $modules): bool
    {

        foreach (Collection::wrap($modules)->toArray() as $module){
            if($this->canUseModule($module)){
                return true;
            }
        }

        return false;
    }

    public function canUseModule(string $module): bool
    {
        return $this->allowed_modules->contains($module);
    }

    public function isUsingModule(string $module): bool
    {
        return $this->enabled_modules->contains($module);
    }

    public function allowModule(string|array $modules, $allow = true): self
    {
        $this->allowed_modules = $allow ? $this->allowed_modules->merge(Collection::wrap($modules))->unique() : $this->allowed_modules->except(Collection::wrap($modules))->unique();

        return $this;
    }

    public function blockModule(string|array $modules): self
    {
        return $this->allowModule($modules, false);
    }

    public function enableModule(string|array $modules): self
    {
        return $this->useModule($modules);
    }

    public function disableModule(string|array $modules): self
    {
        return $this->useModule($modules, false);
    }

    public function useModule(string|array $modules, bool $use = true): self
    {
        $this->enabled_modules = $use ? $this->enabled_modules->merge(Collection::wrap($modules))->unique() : $this->enabled_modules->except(Collection::wrap($modules))->unique();

        return $this;
    }
    //endregion
}