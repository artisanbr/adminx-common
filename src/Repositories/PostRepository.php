<?php

namespace Adminx\Common\Repositories;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Helpers\MorphHelper;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Tag;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PostRepository
{


    public function __construct(
        protected int|null  $page_id = null,
        protected Post|null $post = null
    ) {}

    public function page($page_id): static
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function post(Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Salvar Post
     *
     * @param array $data
     *
     * @return Post|null
     * @throws Throwable
     */
    public function save(array $data): ?Post
    {
        return DB::transaction(function () use ($data) {
            $this->post = Post::findOrNew($data['id'] ?? null);

            $this->post->fill($data);
            $this->post->page_id = $this->page_id;
            $this->post->save();
            $this->post->refresh();

            if ($data['categories'] ?? false) {
                $this->post->categories()->sync($data['categories']);
            }

            if ($data['seo']['keywords'] ?? $data['tags'] ?? false) {
                $tags_titles = $data['tags'] ?? false ? json_decode($data['tags']) : explode(',', $data['seo']['keywords']);

                if (collect($tags_titles)->diff($this->post->tags->pluck('title'))->count()) {
                    $tags = [];

                    foreach ($tags_titles as $tag_title) {
                        $tag = $this->post->site->tags()->where('title', $tag_title)->first() ?? new Tag([
                                                                                                             'title'      => $tag_title,
                                                                                                             'user_id'    => Auth::user()->id,
                                                                                                             'account_id' => Auth::user()->account_id,
                                                                                                             'site_id'    => Auth::user()->site_id,
                                                                                                         ]);
                        $tag->save();
                        $tags[] = $tag->id;
                    }
                    $this->post->tags()->sync($tags);
                    $this->post->page->tags()->sync($tags);
                }
            }

            $this->processUploads($data);
            $this->post->save();

            return $this->post;
        });
    }


    /**
     * @throws Exception
     */
    public function processUploads($data): void
    {
        /**
         * @var array{cover_file?: UploadedFile, seo: array{image_file?: UploadedFile}} $data
         */

        if (!$this->post || !$this->post->site) {
            abort(404, 'Post não encontrado para salvar os arquivos');
        }

        $this->post->refresh();

        $uploadPathBase = "pages/{$this->post->page->public_id}/post/{$this->post->public_id}/images";
        $uploadableType = MorphHelper::resolveMorphType($this->post);

        //Imagem Cover
        if ($data['cover_file'] ?? false) {
            $coverFile = FileHelper::saveRequestToSite($this->post->site, $data['cover_file'], $uploadPathBase, 'cover', $this->post->cover);

            $coverFile->fill([
                                 'type'           => FileType::PostCover,
                                 'title'           => "Imagem de Capa",
                                 'description'     => "Capa de {$uploadableType} #{$this->post->public_id}",
                                 'editable'     => false,
                             ]);
            $coverFile->assignTo($this->post, 'uploadable');

            $this->post->cover_id = $coverFile->id;
        }

        //Imagem SEO
        if ($data['seo']['image_file'] ?? false) {

            $seoFile = FileHelper::saveRequestToSite($this->post->site, $data['seo']['image_file'], $uploadPathBase, 'seo', $this->post->seo->image);

            $seoFile->fill([
                                 'type'           => FileType::PostSeo,
                                 'title'           => "Meta Imagem",
                                 'description'     => "Meta Imagem de {$uploadableType} #{$this->post->public_id}",
                                 'editable'     => false,
                             ]);
            $seoFile->assignTo($this->post, 'uploadable');

            $this->post->seo->image_id = $seoFile->id;
        }
        $this->post->save();
        $this->post->refresh();
    }

    /**
     * Atualizar um Post
     *
     * @param int $id
     * @param     $data
     *
     * @return Post|null
     * @throws Throwable
     */
    public function update(int $id, $data): ?Post
    {
        return $this->save(array_merge($data, ['id' => $id]));
    }

    /**
     * Inserir um Post
     *
     * @param     $data
     *
     * @return Post|null
     * @throws Throwable
     */
    public function insert($data): ?Post
    {
        return $this->save(array_merge($data, ['id' => null]));
    }
}
