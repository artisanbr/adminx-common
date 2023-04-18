<?php

namespace Adminx\Common\Models\Generics\Files;

use Adminx\Common\Models\Bases\Generic\GenericFileBase;
use Adminx\Common\Models\File;

/**
 * @property File $file
 * @property string $html
 */
class GenericImageFile extends GenericFileBase
{

    protected $appends = [
        'html',
        'file'
    ];

    protected function getRenderHtmlAttributesAttribute()
    {
        return $this->html_attributes->merge([
                                                 'alt' => $this->html_attributes->get('title'),
                                             ])->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getHtmlAttribute(): string
    {
        $fileUrl = $this->file->url ?? '';

        return "<img src=\"{$fileUrl}\" {$this->render_html_attributes} />";
    }

    protected function getImageAttribute(): File|null
    {
        return $this->getFileAttribute();
    }

}
