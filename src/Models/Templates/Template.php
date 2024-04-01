<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Scopes\WhereSiteWithNullScope;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Templates\Objects\TemplateConfig;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class Template extends EloquentModelBase
{
    use HasSelect2, HasValidation, HasGenericConfig;


    protected $fillable = [
        'user_id',
        'account_id',
        'site_id',
        'public_id',
        'title',
        'description',
        'path',
        'content',
        'global',
        'morphs',
        'config',
    ];

    protected $casts = [
        'config'      => TemplateConfig::class,
        'public_id'   => 'string',
        'title'       => 'string',
        'content'     => 'string',
        'description' => 'string',
        'morphs'      => 'collection',
        'global'      => 'boolean',
    ];

    protected $appends = [
        'text',
        //'can_use_forms',
        //'can_use_articles',
    ];

    //region SELECT2
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->title ? "<h4>{$this->title}</h4>" . Str::limit($this->description, 150) : "Template sem título ({$this->created_at->shortRelativeToNowDiffForHumans()})",
        );
    }

    //endregion

    /*protected function templateContent(): Attribute
    {
        if ($this->config->use_files) {

            if ($this->config->render_engine === TemplateRenderEngine::Blade) {
                $templateContent = View::make($this->blade_file, $this->getViewRenderData())->render();
            }
            else {
                $templateContent = $this->file_contents;
            }
        }
        else {
            $templateContent = $this->content ?? $this->file_contents ?? null;
        }

        return new Attribute(
            get: static fn() => $templateContent,
        );
    }*/

    public function getTemplateFile($file): string
    {
        return "{$this->full_path}/{$file}";
    }

    public function getTemplateBladeFile($file): string
    {
        return "common-templates::{$this->path}/{$file}";
    }

    public function getTemplateGlobalFile($file): string
    {
        return "{$this->global_path}/{$file}";
    }

    /*public function getTemplateView($view, $data): \Illuminate\Contracts\View\View
    {
        return View::make($this->getTemplateViewFile($view), $data);
    }*/

    //region OVERRIDES
    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteWithNullScope);
    }
    //endregion

    //region Attributes
    protected function content(): Attribute
    {
        return Attribute::make(
            set: fn($value) => (!empty($value) && (string)$value !== (string)$this->file_contents) ? $value : null
        );
    }

    protected function file(): Attribute
    {
        $finalFile = $this->getTemplateFile("{$this->public_id}.twig");

        if (!Storage::drive('ftp')->exists($finalFile)) {
            $finalFile = $this->getTemplateFile('index.twig');

            if (!Storage::drive('ftp')->exists($finalFile)) {
                $finalFile = $this->getTemplateFile('default.twig');

                if (!Storage::drive('ftp')->exists($finalFile)) {
                    $finalFile = null;
                }
            }
        }

        return Attribute::make(get: static fn() => $finalFile);
    }

    protected function globalFile(): Attribute
    {
        $finalFile = $this->getTemplateGlobalFile("{$this->public_id}.twig");

        if (!file_exists($finalFile)) {
            $finalFile = $this->getTemplateGlobalFile("{$this->public_id}/index.twig");

            if (!file_exists($finalFile)) {
                $finalFile = $this->getTemplateGlobalFile("{$this->public_id}/default.twig");

                if (!file_exists($finalFile)) {
                    $finalFile = null;
                }
            }
        }

        return Attribute::make(get: static fn() => $finalFile);
    }

    protected function twigFile(): Attribute
    {
        $finalFile = $this->getTemplateGlobalFile("{$this->public_id}.twig");
        $finalTwig = "@{$this->relative_file}.twig";

        if (!file_exists($finalFile)) {
            $finalFile = $this->getTemplateGlobalFile('index.twig');
            $finalTwig = "@{$this->relative_file}/index.twig";

            if (!file_exists($finalFile)) {
                $finalFile = $this->getTemplateGlobalFile('default.twig');
                $finalTwig = "@{$this->relative_file}/default.twig";

                if (!file_exists($finalFile)) {
                    $finalTwig = null;
                }
            }
        }

        return Attribute::make(get: fn() => $finalTwig);
    }

    protected function bladeFile(): Attribute
    {
        //$finalFile = $this->getTemplateGlobalFile($this->public_id);
        $finalView = $this->getTemplateBladeFile($this->public_id);

        if (!View::exists($finalView)) {
            $finalView = $this->getTemplateBladeFile('index');

            if (!View::exists($finalView)) {
                $finalView = $this->getTemplateBladeFile('default');

                if (!View::exists($finalView)) {
                    $finalView = null;
                }
            }
        }

        return Attribute::make(get: static fn() => $finalView);
    }

    protected function fileContents(): Attribute
    {
        $contents = '';

        if ($this->file) {
            $contents = Storage::drive('ftp')->get($this->file);
        }
        else if ($this->global_file) {
            $contents = file_get_contents($this->global_file);
        }

        return Attribute::make(get: static fn() => $contents);
    }

    protected function fullPath(): Attribute
    {
        $path = $this->global ? $this->global_path : Storage::path($this->path ?? '');

        return Attribute::make(get: static fn() => $path);
    }

    protected function globalPath(): Attribute
    {
        return Attribute::make(get: fn() => GlobalTemplateManager::globalTemplatesPath($this->path));
    }

    protected function relativeFile(): Attribute
    {
        return Attribute::make(get: fn() => ($this->path ? "{$this->path}/" : '') . $this->public_id);
    }

    //region GET's
    //protected function getAttribute(){}
    //endregion

    //region SET's
    //protected function setAttribute(){}

    //endregion
    //endregion

    //region RELATIONS

    public function templatables()
    {
        return $this->morphMany(Templatable::class, 'templatable');
    }

    //endregion
}
