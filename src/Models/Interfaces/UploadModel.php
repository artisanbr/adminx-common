<?php

namespace Adminx\Common\Models\Interfaces;

use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;


interface UploadModel
{
    public function uploadPathTo(?string $path = null): string;
}
