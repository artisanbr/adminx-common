<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Libs\Helpers\MorphHelper;
use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\File;
use ArtisanBR\Adminx\Common\App\Models\Folder;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Post;
use ArtisanBR\Adminx\Common\App\Models\Site;
use ArtisanBR\Adminx\Common\App\Models\Theme;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


/**
 * @var Model|Site|Page|Theme|Post $this
 * @var Model|Site|Page|Theme|Post self
 */
trait HasFiles
{

    protected function uploadPath(): Attribute {
        $path = "";
        if($this instanceof Site){
            $path = "sites/{$this->public_id}";
        }else{
            if($this->site_id ?? false){
                $path = $this->site->upload_path;
            }

            $classDir = Str::of(MorphHelper::getMorphTypeTo(self::class))->plural();

            $path .= "/{$classDir}/".($this->public_id ?? $this->id ?? '');
        }

        return Attribute::make(
            get: fn() => $path
        );
    }

    public function files()
    {
        return $this->morphMany(File::class, 'uploadable');
    }

    public function file(){
        return $this->morphOne(File::class, 'uploadable');
    }

    public function folders()
    {
        return $this->morphMany(Folder::class, 'uploadable');
    }

    public function images()
    {
        return $this->files()->images();
    }

    public function image(){
        return $this->file()->images();
    }
}
