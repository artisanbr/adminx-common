<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Facades\FileManager\FileUploadManager;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Tag;
use Adminx\Common\Repositories\Base\Repository;
use Adminx\Common\Repositories\Traits\SeoModelRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property ?Post $model
 */
class PostRepository extends Repository
{
    use SeoModelRepository;

    protected string $modelClass = Post::class;


    public function __construct(
        protected int|null  $page_id = null,
        protected Post|null $post = null
    ) {

    }

    public function page($page_id): static
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function post(Post $post): static
    {
        $this->setModel($post);

        return $this;
    }

    public function saveTransaction(): ?Post
    {

        $this->model->fill($this->data);
        $this->model->page_id = $this->page_id;
        $this->model->save();
        $this->model->refresh();

        if ($this->data['categories'] ?? false) {
            $this->model->categories()->sync($this->data['categories']);
        }

        if ($this->data['seo']['keywords'] ?? $this->data['tags'] ?? false) {
            $tags_titles = $this->data['tags'] ?? false ? json_decode($this->data['tags']) : explode(',', $this->data['seo']['keywords']);

            if (collect($tags_titles)->diff($this->model->tags->pluck('title'))->count()) {
                $tags = [];

                foreach ($tags_titles as $tag_title) {
                    $tag = $this->model->site->tags()->where('title', $tag_title)->first() ?? new Tag([
                                                                                                         'title'      => $tag_title,
                                                                                                         'user_id'    => Auth::user()->id,
                                                                                                         'account_id' => Auth::user()->account_id,
                                                                                                         'site_id'    => Auth::user()->site_id,
                                                                                                     ]);
                    $tag->save();
                    $tags[] = $tag->id;
                }
                $this->model->tags()->sync($tags);
                $this->model->page->tags()->sync($tags);
            }
        }

        $this->processUploads();
        $this->model->save();

        return $this->model;
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {
        /**
         * @var array{cover_file?: UploadedFile, seo: array{image_file?: UploadedFile}} $data
         */

        if (!$this->model || !$this->model->site) {
            abort(404, 'Post não encontrado para salvar os arquivos');
        }

        $this->model->refresh();

        $this->uploadPathBase = $this->model->uploadPathTo('images');

        //Files
        $coverUploadFile = $this->data['cover_file'] ?? false;
        $seoFile = $this->processSeoUploads();

        //Imagem Cover
        if ($coverUploadFile) {

            $coverFile = FileUpload::upload($coverUploadFile, $this->uploadPathBase, 'cover');

            $this->model->cover_url = $coverFile->url;

            if(!$seoFile){
                $this->model->seo->image_url = $coverFile->url;
            }
        }

        //Imagem SEO
        if ($seoFile) {
            $this->model->seo->image_url = $seoFile->url;
        }

        $this->model->save();
        $this->model->refresh();
    }


}
