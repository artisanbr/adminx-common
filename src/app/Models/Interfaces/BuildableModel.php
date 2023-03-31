<?php

namespace ArtisanBR\Adminx\Common\App\Models\Interfaces;


interface BuildableModel
{
    public function getBuildViewPath($append = null): string;
    public function getBuildViewData(array $requestData = [], array $merge_data = []): array;
}
