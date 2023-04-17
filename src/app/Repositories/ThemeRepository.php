<?php

namespace ArtisanBR\Adminx\Common\App\Repositories;

use ArtisanBR\Adminx\Common\App\Enums\FileType;
use ArtisanBR\Adminx\Common\App\Libs\Helpers\FileHelper;
use ArtisanBR\Adminx\Common\App\Libs\Helpers\MorphHelper;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\BreadcrumbConfig;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes\ThemeMediaElement;
use ArtisanBR\Adminx\Common\App\Models\Theme;
use ArtisanBR\Adminx\Common\App\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * @property  array{media?: ThemeMediaElement, seo: array{image_file?: UploadedFile}} $data
 * @method Theme|null insert(array|Request $data, string $idKey = 'id')
 * @method Theme|null update(int $id, array|Request $data, string $idKey = 'id')
 */
class ThemeRepository extends Repository
{

    public function __construct(
        protected Theme|null $theme = null,
    ) {}

    /**
     * Salvar Tema
     */
    public function save(array|Request $data): ?Theme
    {

        $this->traitData($data);

        return DB::transaction(function () {

            $this->theme = Theme::findOrNew($this->data[$this->idKey] ?? null);

            //$this->theme->header->is_html_advanced = $this->data['header']['is_html_advanced'];
            //$this->theme->footer->is_html_advanced = $this->data['footer']['is_html_advanced'] ?? false;


            $this->theme->fill($this->data);

            //dd($this->data, $this->theme->footer, $this->theme->footer->is_html_advanced, $this->theme->footer->raw);

            if(!$this->theme->config->breadcrumb){
                $this->theme->config->breadcrumb = new BreadcrumbConfig();
            }

            $this->theme->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];

            $this->theme->save();
            $this->theme->refresh();

            if($this->data['is_main'] ?? false){
                $this->theme->refresh();
                $this->theme->site->theme_id = $this->theme->id;
                $this->theme->site->save();
            }


            $this->processUploads();
            $this->theme->save();

            $this->theme->compile();

            return $this->theme;
        });
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->theme || !$this->theme->site) {
            abort(404);
        }

        $this->theme->refresh();

        $this->uploadPathBase = "themes/{$this->theme->public_id}/";
        $this->uploadableType = MorphHelper::resolveMorphType($this->theme);

        //Media

        if($this->data['config']['breadcrumb']['background']['file_upload'] ?? false){

            $mediaFile = FileHelper::saveRequestToSite($this->theme->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->theme->config->breadcrumb->background->file ?? null);

            if(!$mediaFile){
                abort(500);
            }

            $mediaFile->fill([
                                 'title'           => config("adminx.defines.files.default.names.theme.breadcrumb.background", 'breadcrumb-background'),
                                 'type'           => FileType::ThemeMedia,
                                 'description'     => "Media de {$this->uploadableType} #{$this->theme->public_id}",
                                 'editable'     => false,
                             ]);

            $mediaFile->assignTo($this->theme, 'uploadable');

            $this->theme->config->breadcrumb->background->file_id = $mediaFile->id;
        }

        if ($this->data['media'] ?? false) {

            //Passar em todas as medias
            foreach ($this->data['media'] as $attribute => $media) {

                //Verifica se o arquivo foi enviado
                if($media['file_upload'] ?? false){

                    $mediaFile = FileHelper::saveRequestToSite($this->theme->site, $media['file_upload'], $this->uploadPathBase . 'media', $attribute, $this->theme->media->{$attribute}->file ?? null);

                    if(!$mediaFile){
                        abort(500);
                    }

                    $mediaFile->fill([
                                         'title'           => config("adminx.defines.files.default.names.theme.media.{$attribute}", $attribute),
                                         'type'           => FileType::ThemeMedia,
                                         'description'     => "Media de {$this->uploadableType} #{$this->theme->public_id}",
                                         'editable'     => false,
                                     ]);

                    $mediaFile->assignTo($this->theme, 'uploadable');

                    $this->theme->media->{$attribute}->file_id = $mediaFile->id;
                }
            }
        }

        //Todo: Assets
    }
}
