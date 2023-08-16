<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Folder;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Themes\Theme;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


/**
 * @var Model|Site|Page|Theme|Article $this
 * @var Model|Site|Page|Theme|Article self
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
