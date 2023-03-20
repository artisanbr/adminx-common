<?php

namespace ArtisanBR\Adminx\Common\App\Repositories;

use ArtisanBR\Adminx\Common\App\Libs\Helpers\FileHelper;
use ArtisanBR\Adminx\Common\App\Libs\Helpers\MorphHelper;
use ArtisanBR\Adminx\Common\App\Models\File;
use ArtisanBR\Adminx\Common\App\Repositories\Base\Repository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use function App\Repositories\dd;

/**
 * @property  array{seo: array{image_file?: UploadedFile}} $data
 */
class FileRepository extends Repository
{

    public function __construct(
        protected File|null $file = null,
    ) {}

    /**
     * Salvar File
     */
    public function save(array|Request $data): ?File
    {

        $this->traitData($data);

        return DB::transaction(function () {

            $this->file = File::findOrNew($this->data[$this->idKey] ?? null);

            $this->file->fill($this->data);

            dd($this->file->upload($this->data['file_upload'] ?? $this->data['file'], ));

            $this->file->save();
            $this->file->refresh();

            if($this->data['is_main'] ?? false){
                $this->file->refresh();
                $this->file->site->theme_id = $this->file->id;
                $this->file->site->save();
            }

            $this->processUploads();
            $this->file->save();

            return $this->file;
        });
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->file || !$this->file->site) {
            abort(404);
        }

        $this->file->refresh();

        $this->uploadPathBase = "themes/{$this->file->public_id}/";
        $this->uploadableType = MorphHelper::resolveMorphType($this->file);

        //Media
        if ($this->data['media'] ?? false) {

            //Passar em todas as medias
            foreach ($this->data['media'] as $attribute => $media) {

                //Verifica se o arquivo foi enviado
                if($media['file_upload'] ?? false){

                    $mediaFile = FileHelper::saveRequestToSite($this->file->site, $media['file_upload'], $this->uploadPathBase . 'media', $attribute, $this->file->media->{$attribute}->file ?? null);

                    if(!$mediaFile){
                        abort(500);
                    }

                    $mediaFile->fill([
                                         'title'           => config("adminx.defines.files.default.names.theme.media.{$attribute}", $attribute),
                                         'description'     => "Media de {$this->uploadableType} #{$this->file->public_id}",
                                         'editable'     => false,
                                     ]);

                    $mediaFile->assignTo($this->file, 'uploadable');

                    $this->file->media->{$attribute}->file_id = $mediaFile->id;
                }
            }
        }

        //Todo: Assets
    }
}
