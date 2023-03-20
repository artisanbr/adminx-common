<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\Facades\PackageManager;
use Butschster\Head\MetaTags\Meta;
use Butschster\Head\Packages\Package;
use Butschster\Head\Providers\MetaTagsApplicationServiceProvider as ServiceProvider;

class MetaTagsServiceProvider extends ServiceProvider
{
    protected function packages()
    {
        // Create your own packages here
        $this->adminxPackages();
        $this->frontendPackages();
    }

    // if you don't want to change anything in this method just remove it
    protected function registerMeta(): void
    {
        $this->app->singleton(MetaInterface::class, function () {
            $meta = new Meta(
                $this->app[ManagerInterface::class],
                $this->app['config']
            );


            $meta->addCsrfToken();
            $meta = $this->adminxSetup($meta);

            // This method gets default values from config and creates tags, includes default packages, e.t.c
            // If you don't want to use default values just remove it.
            $meta->initialize();

            return $meta;
        });
    }

    protected function adminxSetup(Meta $meta): Meta
    {
        $meta
            ->setDescription(config('adminx.app.info.description'))
            ->setKeywords(config('adminx.app.info.keywords'));

        return $meta;
    }

    protected function adminxPackages(): void
    {
        PackageManager::create('debug-tools', function (Package $package) {
            $package
                //->addScript('console-colors.js', 'https://cdn.jsdelivr.net/npm/@yaireo/console-colors')
                //->addScript('badgee.umd.js',appAsset('vendor/badgee/build/badgee.umd.js'))
                ->addScript('DebugTools.js',appAsset('js/DebugTools/DebugTools.js'))
                ->addScript('DebugToolsAliases.js',appAsset('js/DebugTools/aliases.js'),
                /*[
                    'type' => 'module',
                ]*/);
        });

        PackageManager::create('adminx', function (Package $package) {
            $package->requires(['app','tinymce'])
                ->addScript('AdminX.js',
                          appAsset('js/adminx.bundle.js'));
        });

        //Main
        PackageManager::create('app', function (Package $package) {
            $package
                ->requires('debug-tools')
                //CSS
                ->addStyle('pace-theme-minimal.css',
                           'https://cdn.jsdelivr.net/npm/pace-js@1.2.4/themes/blue/pace-theme-minimal.css')
                ->addStyle('font-inter',
                           'https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap')
                ->addStyle('plugins.bundle.css',
                         appAsset('adminx/plugins/global/plugins.bundle.css'))
                ->addStyle('style.bundle.css',
                         appAsset('adminx/css/style.bundle.css'))
                ->addStyle('css/adminx.css',
                         appAsset('css/adminx.css'))
                ->addStyle('custom.css',
                         appAsset('css/custom.css'))
                ->addStyle('highlight.css',
                           'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/github.min.css')

                //JS
                //Metronic

                ->addScript('adminx-plugins.bundle.js',
                          appAsset('adminx/plugins/global/plugins.bundle.js'))
                ->addScript('adminx-scripts.bundle.js',
                          appAsset('adminx/js/scripts.bundle.js'))
                ->addScript('widgets.bundle.js',
                          appAsset('adminx/js/widgets.bundle.js'))

                //AdminX
                ->addScript('app.js',
                          appAsset('js/app.js'))
                ->addScript('plugins.bundle.js',
                          appAsset('js/plugins.bundle.js'))
                ->addScript('functions.js',
                          appAsset('js/functions.js'))
                ->addScript('modules.bundle.js',
                          appAsset('js/modules.bundle.js'))
                ->addScript('pace.js', 'https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js')
                ->addScript('jsvalidation.js',
                          appAsset('vendor/jsvalidation/js/jsvalidation.js'))
                ->addScript('jquery.formHelper.js',
                          appAsset('js/plugins/jquery/jquery.formHelper.js'))
                ->addScript('jquery.highlight.js',
                          appAsset('js/plugins/jquery/jquery.highlight.js'))
                ->addScript('selectize.min.js',
                          appAsset('vendor/selectize/js/standalone/selectize.min.js'))
                ->addScript('select2-pt-BR.js',
                          appAsset('vendor/select2/i18n/pt-BR.js'))
                ->addScript('quill.htmlEditButton.js',
                            'https://unpkg.com/quill-html-edit-button@2.2.7/dist/quill.htmlEditButton.min.js')
                ->addScript('highlight.min.js',
                            'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/highlight.min.js')
                ->addScript('highlight.xml.js',
                            'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/languages/xml.min.js');

            /*->requires('jquery');*/
        });

        //region CUSTOM PACKAGES


        //autocomplete
        PackageManager::create('autocomplete', function (Package $package) {
            $package
                ->requires('app')
                ->addScript('autocomplete.js',
                          appAsset('vendor/autocomplete/autocomplete.js'));
        });

        PackageManager::create('amcharts', function (Package $package) {
            $package
                ->requires('app')
                ->addScript('amcharts.js','https://cdn.amcharts.com/lib/5/index.js')
                ->addScript('xy.js','https://cdn.amcharts.com/lib/5/xy.js')
                ->addScript('percent.js','https://cdn.amcharts.com/lib/5/percent.js')
                ->addScript('theme.Animated.js','https://cdn.amcharts.com/lib/5/themes/Animated.js')
                ->addScript('map.js','https://cdn.amcharts.com/lib/5/map.js')
                ->addScript('geodata.worldLow.js','https://cdn.amcharts.com/lib/5/geodata/worldLow.js')
                ->addScript('geodata.continentsLow.js','https://cdn.amcharts.com/lib/5/geodata/continentsLow.js')
                ->addScript('geodata.usaLow.js','https://cdn.amcharts.com/lib/5/geodata/usaLow.js')
                ->addScript('geodata.worldTimeZonesLow.js','https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js')
                ->addScript('worldTimeZoneAreasLow.js','https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js');
        });

        //JSTree
        PackageManager::create('jstree', function (Package $package) {
            $package
                ->requires('app')
                ->addStyle(
                    'jstree.bundle.css',
                    appThemeAsset('plugins/custom/jstree/jstree.bundle.css'),
                    ['rel' => 'preload'])
                ->addScript(
                    'jstree.bundle.js',
                    appAsset('vendor/jstree/jstree.bundle.js')
                );
        });

        //JSRender
        PackageManager::create('jsrender', function (Package $package) {
            $package
                ->requires('app')
                ->addScript(
                    'jsrender.js',
                  appAsset('vendor/jsrender/jsrender.min.js'))
                ->addScript(
                    'jsviews.js',
                  appAsset('vendor/jsrender/jsviews.min.js')
                );
        });

        //Jquery Sortable Lists
        PackageManager::create('sortable-lists', function (Package $package) {
            $package
                ->requires('app')
                ->addScript(
                    'jquery-sortable-lists.js',
                  appAsset('js/plugins/jquery-stortable-lists/jquery-sortable-lists.js'));
        });

        //Sortable
        PackageManager::create('sortable', function (Package $package) {
            $package
                ->requires('app')
                ->addScript('draggable.bundle.js','https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js');
        });

        //Draggable
        PackageManager::create('draggable', function (Package $package) {
            $package
                ->requires('app')
                ->addScript(
                    'draggable.bundle.js',
                    appThemeAsset('plugins/custom/draggable/draggable.bundle.js'));
        });

        //Repeater
        PackageManager::create('repeater', function (Package $package) {
            $package
                ->requires('app')
                ->addScript(
                    'repeater.bundle.js',
                  appAsset('js/plugins/jquery/jquery.repeater.js'));
            //assetTheme('plugins/custom/formrepeater/formrepeater.bundle.js'));
        });

        //Ace
        PackageManager::create('ace', function (Package $package) {
            $package
                ->requires('app')
                //CSS
                ->addStyle('ace-colorpicker.css',
                         appAsset('vendor/ace-colorpicker/addon/ace-colorpicker.css'))
                //JS
                ->addScript(
                    'ace.js',
                  appAsset('vendor/ace/ace.js'))
                ->addScript(
                    'mode-html.js',
                  appAsset('vendor/ace/mode-html.js')
                )
                ->addScript(
                    'worker-html.js',
                  appAsset('vendor/ace/worker-html.js')
                )
                ->addScript(
                    'theme-nord_dark.js',
                  appAsset('vendor/ace/theme-nord_dark.js')
                )
                ->addScript(
                    'theme-github.js',
                  appAsset('vendor/ace/theme-github.js')
                )
                ->addScript(
                    'ext-beautify.js',
                  appAsset('vendor/ace/ext-beautify.js')
                )
                ->addScript(
                    'ext-code_lens.js',
                  appAsset('vendor/ace/ext-code_lens.js')
                )
                ->addScript(
                    'ext-emmet.js',
                  appAsset('vendor/ace/ext-emmet.js')
                )
                ->addScript(
                    'ext-language_tools.js',
                  appAsset('vendor/ace/ext-language_tools.js')
                )
                ->addScript(
                    'ext-options.js',
                  appAsset('vendor/ace/ext-options.js')
                )
                ->addScript(
                    'ext-prompt.js',
                  appAsset('vendor/ace/ext-prompt.js')
                )
                ->addScript(
                    'ext-statusbar.js',
                  appAsset('vendor/ace/ext-statusbar.js')
                )
                ->addScript(
                    'ext-spellcheck.js',
                  appAsset('vendor/ace/ext-spellcheck.js')
                )
                ->addScript(
                    'ext-settings_menu.js',
                  appAsset('vendor/ace/ext-settings_menu.js')
                )
                ->addScript(
                    'ext-searchbox.js',
                  appAsset('vendor/ace/ext-searchbox.js')
                )
                ->addScript(
                    'ace-colorpicker.js',
                  appAsset('vendor/ace-colorpicker/addon/ace-colorpicker.js')
                );
        });

        //Datatables
        PackageManager::create('datatables', function (Package $package) {
            $package
                ->requires(['adminx'])
                //CSS
                ->addStyle('datatables.bundle.css',
                           appThemeAsset('plugins/custom/datatables/datatables.bundle.css'))
                //JS
                ->addScript(
                    'datatables.bundle.js',
                    appThemeAsset('plugins/custom/datatables/datatables.bundle.js'))
                ->addScript(
                    'datatables-buttons.server-side.js',
                  appAsset('vendor/datatables/buttons.server-side.js')
                )
                ->addScript(
                    'datatables-datetime-moment.js',
                  appAsset('vendor/datatables/plugins/sorting/datetime-moment.js')
                );
        });

        //TinyMCE
        PackageManager::create('tinymce', function (Package $package) {
            $package
                ->requires(['adminx', 'ace'])
                /*->addScript(
                    'tinymce.bundle.js',
                    assetTheme('plugins/custom/tinymce/tinymce.bundle.js'))
                ->addScript(
                    'tinymce.plugin.ace.js',
                  appAsset('vendor/tinymce/plugins/ace/plugin.js'))
                ->addScript(
                    'tinymce.lang.pt_BR.js',
                  appAsset('vendor/tinymce/langs/pt_BR.js'))*/
                ->addScript(
                    'tinymce.js',
                  appAsset('vendor/tinymce/tinymce.min.js'),
                    ['referrerpolicy' => 'origin']);
        });

        //CKEditor
        PackageManager::create('ckeditor', function (Package $package) {
            $package
                ->requires(['adminx', 'ace'])
                ->addScript(
                    'ckeditor.js',
                  appAsset('vendor/ckeditor/build/ckeditor.min.js'))/* ->addScript(
                    'ckeditor-classic.bundle.j',
                    assetTheme('plugins/custom/ckeditor/ckeditor-classic.bundle.js'))*/
            ;
        });

        //endregion


    }

    protected function frontendPackages(): void
    {
        //region Plugins
        PackageManager::create('jquery', function (Package $package) {
            $package->addScript('jquery.js', 'https://code.jquery.com/jquery-' . config('adminx.themes.versions.jquery') . '.min.js', [
                'integrity'   => 'sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=',
                'crossorigin' => 'anonymous',
                //'defer'
                
            ]);
        });

        //region Bootstrap 5
        PackageManager::create('boostrap:5-js', function (Package $package) {
            $package->addScript('boostrap.bundle.js', 'https://cdn.jsdelivr.net/npm/bootstrap@' . config('adminx.themes.versions.bootstrap:5') . '/dist/js/bootstrap.bundle.min.js', [
                'crossorigin'    => 'anonymous',
                'referrerpolicy' => 'no-referrer',
                'defer'
                
            ])
                    ->addScript('theme.bs5.js',
                                frontendAsset('js/theme.main.bs5.js'));
        });

        PackageManager::create('boostrap:5-css', function (Package $package) {
            $package->addStyle('bootstrap.css', 'https://cdn.jsdelivr.net/npm/bootstrap@' . config('adminx.themes.versions.bootstrap:5') . '/dist/css/bootstrap.min.css', [
                'crossorigin'    => 'anonymous',
                'referrerpolicy' => 'no-referrer',
                
            ]);
        });

        PackageManager::create('boostrap:5', function (Package $package) {
            $package->requires(['jquery', 'boostrap:5-css', 'boostrap:5-js']);
        });
        //endregion

        //region Bootstrap 4
        PackageManager::create('boostrap:4-js', function (Package $package) {
            $package->addScript('boostrap.bundle.js', 'https://cdn.jsdelivr.net/npm/bootstrap@' . config('adminx.themes.versions.bootstrap:4') . '/dist/js/bootstrap.bundle.min.js', [
                'crossorigin'    => 'anonymous',
                'referrerpolicy' => 'no-referrer',
                'defer',
                
            ])
                    ->addScript('theme.bs4.js',
                                frontendAsset('js/theme.main.bs4.js') );
        });

        PackageManager::create('boostrap:4-css', function (Package $package) {
            $package->addStyle('bootstrap.css', 'https://cdn.jsdelivr.net/npm/bootstrap@' . config('adminx.themes.versions.bootstrap:4') . '/dist/css/bootstrap.min.css', [
                'crossorigin'    => 'anonymous',
                'referrerpolicy' => 'no-referrer',
                'defer',

            ]);
        });

        PackageManager::create('boostrap:4', function (Package $package) {
            $package->requires(['jquery', 'boostrap:4-css', 'boostrap:4-js']);
        });
        //endregion

        PackageManager::create('axios', function (Package $package) {
            $package->addScript('axios.js', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js');
        });

        PackageManager::create('lodash', function (Package $package) {
            $package->addScript('lodash.js', 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js');
        });

        PackageManager::create('wnumb', function (Package $package) {
            $package->addScript('wNumb.js', 'https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.2.0/wNumb.min.js');
        });

        PackageManager::create('moment', function (Package $package) {
            $package->addScript('moment.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js')
                    ->addScript('br.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/br.min.js')
                    ->addScript('pt-br.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/pt-br.min.js');
        });

        PackageManager::create('toastr', function (Package $package) {
            $package->addStyle('toastr.css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css', [
                'rel'    => 'stylesheet',
                'media'  => 'print',
                'onload' => "this.media='all'",
                ])
                    ->addScript('toastr.js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', ['async']);
        });

        PackageManager::create('sweetalert', function (Package $package) {
            $package->addScript('sweetalert.js', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js');
        });

        PackageManager::create('autosize', function (Package $package) {
            $package->addScript('autosize.js', 'https://cdnjs.cloudflare.com/ajax/libs/autosize.js/3.0.20/autosize.min.js');
        });

        ////region Layout Plugins

        $plataformPlugins = config('adminx.themes.plugins');

        foreach ($plataformPlugins as $name => $plugin) {
            PackageManager::create($name, function (Package $package) use ($plugin) {

                if (isset($plugin['css'])) {
                    foreach ($plugin['css'] as $nameCSS => $pluginCSS) {
                        $package->addStyle($nameCSS, $pluginCSS['src'], $pluginCSS['attributes'] ?? []);
                    }
                }

                if (isset($plugin['js'])) {
                    foreach ($plugin['js'] as $nameJS => $pluginJS) {
                        $package->addScript($nameJS, $pluginJS['src'], $pluginJS['attributes'] ?? []);
                    }

                }


            });
        }

        //endregion

        //endregion

        //region Main
        PackageManager::create('frontend.pre', function (Package $package) {

            $package
                ->requires(['axios', 'lodash', 'wnumb', 'moment', 'toastr', 'sweetalert', 'autosize'])

                //CSS
                ->addStyle('pace-theme-minimal.css',
                           'https://cdn.jsdelivr.net/npm/pace-js@1.2.4/themes/blue/pace-theme-minimal.css')
                ->addStyle('font-awesome',
                           'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css',
                           [
                               'integrity'      => 'sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==',
                               'crossorigin'    => 'anonymous',
                               'referrerpolicy' => 'no-referrer',
                               'rel'    => 'stylesheet',
                               'media'  => 'print',
                               'onload' => "this.media='all'",

                           ])
                /*->addStyle('all.min.css',
                           frontendThemeAsset('css/all.min.css'),
                )
                ->addStyle('main.c64b6eb2.css',
                           frontendThemeAsset('static/css/main.c64b6eb2.css'))*/

                //JS

                ->addScript('functions.js',
                          appAsset('js/functions.js'))
                ->addScript('pace.js',
                            'https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js')
                ->addScript('jquery.formHelper.js',
                          appAsset('js/plugins/jquery/jquery.formHelper.js'), ['defer'])
                ->addScript('jsvalidation.js',
                          appAsset('vendor/jsvalidation/js/jsvalidation.js'), ['defer'])
                ->addScript('modules.bundle.js',
                          appAsset('js/modules.bundle.js'), ['defer'])
                ->addScript('theme.main.js',
                            frontendAsset('js/theme.main.js'), ['defer'])
                ->addScript('slick.min.js',
                            '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js');

            /*->requires('jquery');*/
        });
        PackageManager::create('frontend.pos', function (Package $package) {

            $package
                //CSS
                ->addStyle('theme.main.css',
                           frontendAsset('css/theme.main.css'), [
                               'rel'    => 'stylesheet',
                               'media'  => 'print',
                               'onload' => "this.media='all'",

                           ])
                ->addStyle('theme.custom.css',
                           frontendAsset('css/theme.custom.css'), [
                               'rel'    => 'stylesheet',
                               'media'  => 'print',
                               'onload' => "this.media='all'",

                           ]);
        });
        //endregion
    }
}
