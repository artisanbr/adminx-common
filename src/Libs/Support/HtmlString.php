<?php

namespace Adminx\Common\Libs\Support;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Illuminate\Support\HtmlString as HtmlStringCore;

class HtmlString extends HtmlStringCore
{
    public function fixTags(): static
    {
        $this->html = HtmlHelper::fixTags($this->html);

        return $this;
    }

    public function toHtml(): string
    {
        $this->fixTags();

        return parent::toHtml();
    }
}