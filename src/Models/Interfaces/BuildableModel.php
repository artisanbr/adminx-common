<?php

namespace Adminx\Common\Models\Interfaces;


interface BuildableModel
{
    public function getBuildViewPath($append = null): string;
    public function getBuildViewData(array $merge_data = []): array;
}
