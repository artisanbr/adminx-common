<?php

namespace ArtisanBR\Adminx\Common\App\Models\Generics\Files;

use ArtisanBR\Adminx\Common\App\Models\Bases\Generic\GenericFileBase;
use ArtisanBR\Adminx\Common\App\Models\File;

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
