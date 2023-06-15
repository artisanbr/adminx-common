<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\Configs\PageConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{config?: PageConfig, seo: array{image_file?: UploadedFile}} $data
 * @method Page|null insert(array|Request $data, string $idKey = 'id')
 * @method Page|null update(int $id, array|Request $data, string $idKey = 'id')
 *
 */
class PageRepository extends Repository
{

    public function __construct(
        protected Page|null $page = null,
    ) {}

    /**
     * Salvar Tema
     */
    public function save(array|Request $data): ?Page
    {

        $this->traitData($data);

        return DB::transaction(function () {

            $this->page = Page::findOrNew($this->data[$this->idKey] ?? null);

            //$this->theme->header->is_html_advanced = $this->data['header']['is_html_advanced'];
            //$this->theme->footer->is_html_advanced = $this->data['footer']['is_html_advanced'] ?? false;


            $this->page->fill($this->data);


            $this->page->save();
            $this->page->refresh();
            //dd('chegou', $this->page);

            //Generate breadcrumb
            $this->page->config->breadcrumb = $this->page->config->breadcrumb ?? $this->page->site->theme->config->breadcrumb ?? new BreadcrumbConfig();

            $this->page->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];

            //$this->page->config->useModule('data_source', (bool) ($this->page->config->source->id ?? false));

            $this->page->save();

            $this->processUploads();
            $this->page->save();

            return $this->page;
        });
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->page || !$this->page->site) {
            abort(404);
        }

        $this->page->refresh();

        $this->uploadPathBase = "pages/{$this->page->public_id}/";
        $this->uploadableType = MorphHelper::resolveMorphType($this->page);

        //Breadcrumb
        if($this->data['config']['breadcrumb']['background']['file_upload'] ?? false){

            $mediaFile = FileHelper::saveRequestToSite($this->page->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->page->config->breadcrumb->background->file ?? null);

            if(!$mediaFile){
                abort(500);
            }

            $mediaFile->fill([
                                 'title'           => config("adminx.defines.files.default.names.theme.breadcrumb.background", 'breadcrumb-background'),
                                 'type'           => FileType::PageUpload,
                                 'description'     => "Media de {$this->uploadableType} #{$this->page->public_id}",
                                 'editable'     => false,
                             ]);

            $mediaFile->assignTo($this->page, 'uploadable');

            $this->page->config->breadcrumb->background->file_id = $mediaFile->id;
        }

        //Imagem SEO
        if ($this->data['seo']['image_file'] ?? false) {

            $seoFile = FileHelper::saveRequestToSite($this->page->site, $this->data['seo']['image_file'], $this->uploadPathBase, 'seo', $this->page->seo->image);

            $seoFile->fill([
                               'type'           => FileType::PostSeo,
                               'title'           => "Meta Imagem",
                               'description'     => "Meta Imagem de {$this->uploadableType} #{$this->page->public_id}",
                               'editable'     => false,
                           ]);
            $seoFile->assignTo($this->page, 'uploadable');

            $this->page->seo->image_id = $seoFile->id;
        }

        //Todo: Assets
    }
}
