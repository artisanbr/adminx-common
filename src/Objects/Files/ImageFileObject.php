<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Objects\Files;


use Adminx\Common\Libs\Support\Html;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\FileNotPreviewableException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @property bool                                    $is_image
 * @property bool                                    $is_webp_compatible
 * @property bool                                    $is_zip
 * @property bool                                    $is_editable
 * @property TemporaryUploadedFile|UploadedFile|null $uploaded_file
 */
class ImageFileObject extends FileObject
{

    protected $temporary = [
        'location',
        'uploaded_file',
        'rename_to',
        'relative_path',
        'is_image',
        'is_webp_compatible',
        'is_zip',
        'is_editable',
    ];

    public function __construct(array $attributes = [])
    {
        $this->addFillables([
                                'url',
                                'external',
                                'width',
                                'height',
                                'class',
                                'alt',
                            ]);

        $this->addCasts([
                            'url'      => 'string',
                            'external' => 'boolean',
                            'width'    => 'string',
                            'height'   => 'string',
                            'class'    => 'string',
                            'alt'      => 'string',
                        ]);
        parent::__construct($attributes);
    }

    //region Helpers

    /**
     * Sync image height and width from file
     */
    public function syncSize(): void
    {
        if (!blank($this->path)) {
            $remoteStorage = Storage::disk('ftp');
            $file = $remoteStorage->get($this->path);
            if ($file) {
                $image = Image::read($file);

                if ($image) {
                    $this->attributes['width'] = $image->width();
                    $this->attributes['height'] = $image->height();
                }
            }
        }


    }

    public function getFileName(bool $updateObject = false): string
    {
        if ($updateObject) {
            $this->attributes['name'] = basename($this->getUrlPathAttribute() ?? $this->getPathAttribute());

            return $this->attributes['name'];
        }

        return basename($this->getUrlPathAttribute() ?? $this->getPathAttribute());
    }

    /**
     * File URl including possible uploaded file
     *
     * @throws FileNotPreviewableException
     */
    public function getLiveUrl(): ?string
    {
        return ($this->uploaded_file instanceof TemporaryUploadedFile) ? $this->uploaded_file->temporaryUrl() : $this->getUrlAttribute();
    }

    /**
     * Is an empty object?
     */
    public function empty(): ?string
    {
        return blank($this->attributes['url'] ?? null) && blank($this->attributes['path'] ?? null);
    }
    //endregion

    //region Attributes
    //region GET's
    //protected function getAttribute(){return $this->attributes[""];}

    protected function getDirectoryAttribute(): ?string
    {
        if ($this->external) {
            return null;
        }

        return parent::getDirectoryAttribute();

        $urlStr = str($this->getUrlPathAttribute())->beforeLast($this->getNameAttribute());

        if ($urlStr->startsWith(['storage/', '/storage/'])) {
            $urlStr = $urlStr->after('storage/');
        }


        return $urlStr->start('/')->finish('/')->toString();
    }

    protected function getRelativePathAttribute(): string
    {
        return $this->getDirectoryAttribute();
    }

    protected function getNameAttribute(): string
    {
        if (blank($this->attributes['name'] ?? null)) {
            return $this->getFileName(true);
        }

        return $this->attributes['name'];
    }

    protected function getPathAttribute(): ?string
    {
        if ($this->external) {
            return null;
        }

        $urlOrPath = str($this->attributes['path'] ?? $this->getUrlPathAttribute())->start('/');

        if ($urlOrPath->startsWith('/storage/')) {
            $urlOrPath = $urlOrPath->after('/storage/');
        }

        return $urlOrPath->start('/');
    }

    protected function getUrlAttribute(): ?string
    {

        if ($this->empty()) {
            return null;
        }

        return $this->attributes['url'] ?? "/storage/" . $this->getPathAttribute();
    }

    protected function getUrlPathAttribute(): ?string
    {

        if (blank($this->attributes['url'] ?? null)) {
            return null;
        }

        return parse_url($this->attributes['url'], PHP_URL_PATH) ?? str($this->attributes['url'])->after('://')->after('/')->toString();
    }

    protected function getHtmlAttribute(): string
    {

        $attrs = Html::attributesFromArray([
                                               'src'   => $this->getUrlAttribute(),
                                               'alt'   => $this->alt,
                                               'class' => $this->class,
                                           ]);

        return "<img {$attrs} />";
    }

    protected function getFullHtmlAttribute(): string
    {
        $attrs = Html::attributesFromArray([
                                               'src'    => $this->getUrlAttribute(),
                                               'alt'    => $this->alt,
                                               'class'  => $this->class,
                                               'height' => $this->height,
                                               'width'  => $this->width,
                                           ]);

        return "<img {$attrs} />";
    }


    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion
}
