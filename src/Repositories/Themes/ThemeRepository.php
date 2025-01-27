<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories\Themes;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeMediaElement;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Repositories\Base\Repository;
use App\Libs\Utils\FrontendUtils;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

/**
 * @property  array{media?: ThemeMediaElement, seo: array{image_file?: UploadedFile}} $data
 * @property ?Theme                                                                   $model
 */
class ThemeRepository extends Repository
{

    protected string $modelClass = Theme::class;

    protected Filesystem $remoteStorage, $tempStorage;

    public function __construct()
    {

        $this->remoteStorage = Storage::disk('ftp');
        $this->tempStorage = Storage::disk('temp');
    }

    /**
     * Salvar Tema
     */
    public function saveTransaction(): ?Theme
    {
        //$this->model->header->is_html_advanced = $this->data['header']['is_html_advanced'];
        //$this->model->footer->is_html_advanced = $this->data['footer']['is_html_advanced'] ?? false;


        //dump($this->model->config);

        $this->model->fill($this->data);

        //dd($this->data['config'], $this->model->config);
        //dd($this->model->media);

        if (!$this->model->config->breadcrumb) {
            $this->model->config->breadcrumb = new BreadcrumbConfig();
        }

        $this->model->config->breadcrumb->default_items = $this->data['config']['breadcrumb']['default_items'] ?? [];

        $this->model->save();
        //$this->model->refresh();

        if ($this->data['is_main'] ?? false) {
            //$this->model->refresh();
            $this->model->site->theme_id = $this->model->id;
            $this->model->site->save();
        }


        $this->processUploads();
        $this->model->save();

        if($this->data['publish_theme'] ?? false){
            $this->generateBundles($this->model);
        }else{

            $this->model->generateBuild();
        }


        return $this->model;
    }


    /**
     * @throws Exception
     */
    public function processUploads(): void
    {

        if (!$this->model || !$this->model->site) {
            abort(404);
        }

        //$this->model->refresh();

        $this->uploadPathBase = $this->model->storagePathTo('media');

        //$this->uploadPathBase = "themes/{$this->model->public_id}";
        //$this->uploadableType = MorphHelper::resolveMorphType($this->model);

        //Media

        if ($this->data['config']['breadcrumb']['background']['file_upload'] ?? false) {

            //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase . 'breadcrumb', 'background', $this->model->config->breadcrumb->background->file ?? null);

            $breadcrumbFile = FileUpload::upload($this->data['config']['breadcrumb']['background']['file_upload'], $this->uploadPathBase, 'breadcrumb');

            if (!$breadcrumbFile) {
                abort(500);
            }

            $this->model->config->breadcrumb->background->url = $breadcrumbFile->url;
        }

        if ($this->data['media'] ?? false) {

            //Passar em todas as medias
            foreach ($this->data['media'] as $attribute => $media) {

                //Verifica se o arquivo foi enviado
                if ($media['file_upload'] ?? false) {

                    //$mediaFile = FileHelper::saveRequestToSite($this->model->site, $media['file_upload'], $this->uploadPathBase . 'media', $attribute, $this->model->media->{$attribute}->file ?? null);

                    $mediaFile = FileUpload::upload($media['file_upload'], $this->uploadPathBase, $attribute);

                    if (!$mediaFile) {
                        abort(500);
                    }

                    $this->model->media->{$attribute}->url = $mediaFile->url;
                }
            }
        }

        //Todo: Assets
    }


    public function generateBundles(Theme $theme): void
    {
        //$theme->saveAndBuild();

        $themeBuild = $theme->build()->firstOrNew([
                                                      'site_id'    => $theme->site_id,
                                                      'user_id'    => $theme->user_id,
                                                      'account_id' => $theme->account_id,
                                                  ]);

        $themeBuild->save();


        //Libraries CSS e JS //todo: virar resources
        //$headCssCompileFiles = $headCssCompileFiles->merge($theme->config->libs->cdn_css_compile_files);
        //$bodyJsFiles = $bodyJsFiles->merge($theme->config->libs->cdn_js_compile_files);

        //Plugins da plataforma //todo: virar resources
        /*$platformPlugins = collect(config('adminx.themes.plugins'));
        $themePlugins = $theme->config->plugins->toArray();

        $enabledPlugins = $platformPlugins->only($themePlugins);

        $enabledPluginsCss = $enabledPlugins->filter(fn($plugin) => !isset($plugin['skip-compile']) || !$plugin['skip-compile'])->pluck('css')->flatten(1)->pluck('src')->filter();
        $enabledPluginsJs = $enabledPlugins->pluck('js')->flatten(1)->pluck('src')->filter();

        $headCssCompileFiles = $headCssCompileFiles->merge($enabledPluginsCss->toArray());
        $bodyJsFiles = $bodyJsFiles->merge($enabledPluginsJs->toArray());*/

        //Bundles: main, defer, main-head, defer-head

        //region main & defer css

        //Theme Resources
        $themeCssResources = $theme->assets->resources->css;

        //Resources css com "agrupar" habilitado e "adiar" desabilitado
        $cssMainResources = $themeCssResources->bundleMainList()
                                              ->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));

        //Resources css com "agrupar" habilitado e "adiar" habilitado
        $cssDeferResources = $themeCssResources->bundleDeferList()
                                               ->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));

        /*$cssBuild = $headCssIncludeFiles->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");
        $cssBuild .= "\n" . $headCssCompileFiles->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");*/

        /*$cssBuild .= "\n" . $cssIncludeResources->map(fn($fileUrl) => $this->compileCss($fileUrl))->implode("\n");*/

        $cssMainBundleContent = $cssMainResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");
        $cssDeferBundleContent = $cssDeferResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");

        if($this->model->config->bundle_stuffs) {
            $cssDeferBundleContent .= "\n" . $this->getAssetFileContent(FrontendUtils::asset('css/theme.main.css'));
            $cssDeferBundleContent .= "\n" . $this->getAssetFileContent(FrontendUtils::asset('css/theme.custom.css'));
        }

        //endregion

        //region main & defer js

        $bodyJsMainResources = $theme->assets->resources->js->bundleMainList()->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));
        $bodyJsDeferResources = $theme->assets->resources->js->bundleDeferList()->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));

        $headJsMainResources = $theme->assets->resources->head_js->bundleMainList()->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));
        $headJsDeferResources = $theme->assets->resources->head_js->bundleDeferList()->map(fn($assetItem) => $assetItem->external ? $assetItem->url : $theme->cdnProxyUploadUrlTo($assetItem->url));

        $bodyJsMainBundleContent = $bodyJsMainResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");
        $bodyJsDeferBundleContent = $bodyJsDeferResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");

        $headJsMainBundleContent = $headJsMainResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");
        $headJsDeferBundleContent = $headJsDeferResources->map(fn($fileUrl) => $this->getAssetFileContent($fileUrl))->implode("\n");


        if($this->model->config->bundle_stuffs){
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent(appAsset('js/functions.js'));
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent(appAsset('js/plugins/jquery/jquery.formHelper.js'));
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent(FrontendUtils::asset('js/modules.bundle.js'));
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent(FrontendUtils::asset('js/theme.main.js'));

            //CDN Padrão
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js");
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js");
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js");
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js");
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/br.min.js");
            $bodyJsMainBundleContent .= "\n" . $this->getAssetFileContent("https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.min.js");
        }


        //endregion


        //region 3. Compactar e Salvar a string em um arquivo individual
        //Compactar
        $cssMainBundleContentMin = (new CSS())->add($cssMainBundleContent)->minify();
        $cssDeferBundleContentMin = (new CSS())->add($cssDeferBundleContent)->minify();
        $headJsMainBundleContentMin = (new JS())->add($headJsMainBundleContent)->minify();
        $headJsDeferBundleContentMin = (new JS())->add($headJsDeferBundleContent)->minify();
        $bodyJsMainBundleContentMin = (new JS())->add($bodyJsMainBundleContent)->minify();
        $bodyJsDeferBundleContentMin = (new JS())->add($bodyJsDeferBundleContent)->minify();


        //region Armazenar bundles no banco

        $mainCssBundle = $themeBuild->main_css_bundle->fill([
                                                                'content' => $cssMainBundleContentMin,
                                                            ]);
        $deferCssBundle = $themeBuild->defer_css_bundle->fill([
                                                                  'content' => $cssDeferBundleContentMin,
                                                              ]);

        $mainBodyJsBundle = $themeBuild->main_body_js_bundle->fill([
                                                                       'content' => $bodyJsMainBundleContentMin,
                                                                   ]);
        
        $deferBodyJsBundle = $themeBuild->defer_body_js_bundle->fill([
                                                                         'content' => $bodyJsDeferBundleContentMin,
                                                                     ]);

        $mainHeadJsBundle = $themeBuild->main_head_js_bundle->fill([
                                                                       'content' => $headJsMainBundleContentMin,
                                                                   ]);
        $deferHeadJsBundle = $themeBuild->defer_head_js_bundle->fill([
                                                                         'content' => $headJsDeferBundleContentMin,
                                                                     ]);

        /*$themeBuild->fill([
                              'bundles' => [
                                  'css_main'      => $cssMainBundleContentMin,
                                  'css_defer'     => $cssDeferBundleContentMin,
                                  'head_js_main'  => $headJsMainBundleContentMin,
                                  'head_js_defer' => $headJsDeferBundleContentMin,
                                  'js_main'       => $bodyJsMainBundleContentMin,
                                  'js_defer'      => $bodyJsDeferBundleContentMin,
                              ],
                          ]);*/

        //dd('chegou');
        //endregion

        //region Salvar Arquivos

        //Limpar cache
        $this->remoteStorage->delete($theme->cdnProxyUrlTo('bundles'));
        $this->remoteStorage->makeDirectory($theme->cdnProxyUrlTo('bundles'));

        //Armazenar Compactado e Original
        $this->remoteStorage->put($mainCssBundle->file_path, $cssMainBundleContent);
        $this->remoteStorage->put($mainCssBundle->file_path_minified, $cssMainBundleContentMin);

        $this->remoteStorage->put($deferCssBundle->file_path, $cssDeferBundleContent);
        $this->remoteStorage->put($deferCssBundle->file_path_minified, $cssDeferBundleContentMin);

        $this->remoteStorage->put($mainBodyJsBundle->file_path, $bodyJsMainBundleContent);
        $this->remoteStorage->put($mainBodyJsBundle->file_path_minified, $bodyJsMainBundleContentMin);

        $this->remoteStorage->put($deferBodyJsBundle->file_path, $bodyJsDeferBundleContent);
        $this->remoteStorage->put($deferBodyJsBundle->file_path_minified, $bodyJsDeferBundleContentMin);


        $this->remoteStorage->put($mainHeadJsBundle->file_path, $headJsMainBundleContent);
        $this->remoteStorage->put($mainHeadJsBundle->file_path_minified, $headJsMainBundleContentMin);

        $this->remoteStorage->put($deferHeadJsBundle->file_path, $headJsDeferBundleContent);
        $this->remoteStorage->put($deferHeadJsBundle->file_path_minified, $headJsDeferBundleContentMin);

        $mainCssBundle->save();
        $deferCssBundle->save();
        $mainBodyJsBundle->save();
        $deferBodyJsBundle->save();
        $mainHeadJsBundle->save();
        $deferHeadJsBundle->save();

        /*if (!blank($mainCssBundle->content)) {


        }
        if (!blank($deferCssBundle->content)) {
        }

        if (!blank($mainBodyJsBundle->content)) {
        }

        if (!blank($deferBodyJsBundle->content)) {
        }

        if (!blank($mainHeadJsBundle->content)) {
        }

        if (!blank($deferHeadJsBundle->content)) {
        }*/
        //endregion


        //endregion

        //Build Theme Again
        $theme->generateBuild();
        //$theme->saveAndBuild();
    }

    protected function compileCss(string $fileUrl): string
    {
        return "@import url(\"{$fileUrl}\");";;
    }

    protected function getAssetFileContent(string $fileUrl): string
    {
        return str($fileUrl)->startsWith([
                                             'http',
                                             '//',
                                         ]) ? $this->getExternalFileContent($fileUrl) : $this->getRemoteStorageFileContent($fileUrl);
    }

    protected function getExternalFileContent($fileUrl): string
    {
        //dd('chegou');
        $fileResponse = Http::get($fileUrl);

        if ($fileResponse->successful()) {
            $contentBody = $fileResponse->body();
            if (!blank($contentBody)) {
                return $contentBody;
            }
        }

        return '';
    }

    protected function getRemoteStorageFileContent($fileUrl): string
    {
        return $this->remoteStorage->get($fileUrl) ?? '';
    }
}
