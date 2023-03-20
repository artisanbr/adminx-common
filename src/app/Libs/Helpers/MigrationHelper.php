<?php
/**
 * Created by PhpStorm.
 * User: renalcio
 * Date: 11/10/16
 * Time: 16:52
 */
namespace ArtisanBR\Adminx\Common\App\Libs\Helpers;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MigrationHelper extends Blueprint {

    /**
     * @var Migrator
     */
    private $migrator;

    private $app;

    public $files;

    public $paths;

    public $tables;

    public $migrations;

    public $artisan;

    public $models;

    private $exceptions = [];//['created_at', 'updated_at', 'deleted_at'];

    private $default_timestamps = ['created_at', 'updated_at', 'deleted_at'];

    private $hasSoftDelete = false;

    public $bar;

    public function __construct($artisan = null){

        //prepara app
        $this->app = app();

        //prepara Migrator
        $this->migrator = $this->app['migrator'];

        //Path das Migration Files
        $this->paths = [$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'];

        //Migration Files
        $this->files = $this->migrator->getMigrationFiles($this->paths);



        $this->table = $this->models = $this->migrations = [];

        $this->artisan = $artisan;

        $this->blueprint();

    }

    public function start(){
        $this->setFillables();
        $this->setType('hiddens', ['hidden', 'hiddenfield']);
        $this->setType('timestamps', ["timestamp"]);
        $this->setType('dates', ['date',"time",'datetime']);

        //Diretorio das Properties
        $base_dir = app_path("Models".DIRECTORY_SEPARATOR."Properties");

        $filesystem = new Filesystem();

        //Cria diretorio se nao existir
        if(!$filesystem->exists($base_dir)){
            $filesystem->makeDirectory($base_dir, 493, true);
        }

        //Verifica opção de merge
        $merge = $this->artisan->option("merge");

        $this->artisan->comment("Criando Properties");

        //passa model a model e salvar arquivos
        foreach ($this->models as $m => $model) {

            $filename =  ucfirst($m)."Properties.php";

            $file = $base_dir.DIRECTORY_SEPARATOR.$filename;

            if(!$merge || !$filesystem->exists($file)) {

                $template = $this->setTemplate($m);
                //$this->artisan->comment(var_dump($template));

                $filesystem->put($file, $template);

                if($filesystem->exists($file))
                    $this->artisan->comment("Reescrevendo: ".$file);
                else
                    $this->artisan->comment("Criado: ".$file);

            }else{
                $this->artisan->comment("Ignorando: ".$file." Arquivo ja existe");
            }

            $this->artisan->bar->advance();
        }

        if($this->artisan->option("model")){
            $this->artisan->comment("Criando Models");

            //Diretorio das Properties
            $base_dir = app_path("Models");

            //passa model a model e salvar arquivos
            foreach ($this->models as $m => $model) {
                $filename =  ucfirst($m).".php";

                $file = $base_dir.DIRECTORY_SEPARATOR.$filename;

                if(!$filesystem->exists($file)) {
                    $template = $this->setModelTemplate($m);
                    $filesystem->put($file, $template);
                    $this->artisan->comment("Criado: ".$file);
                }else{
                    $this->artisan->comment("Ignorando: ".$file." Arquivo ja existe");
                }

                $this->artisan->bar->advance();
            }
        }
        $this->artisan->bar->finish();
    }

    /**
     * Define as colunas sem a opção nofillable ou nofill
     */
    public function setFillables(){

        $this->artisan->comment("Buscando Fillables");

        //Passa tabela a tabela
        foreach ($this->tables as   $t => $table) {

            $this->artisan->comment("Tabela: ".$t);

            if(isset($this->models[$t]) && !is_array($this->models[$t]))
                $this->models[$t] = [];

            $colunas = $table->getColumns();

            //Passa coluna a coluna
            foreach ($colunas as $c => $coluna) {

                $nome = $coluna->get('name', "");

                //Adiciona a lista se não continver nofill e noffilable, tiver um nome e este nome nao estiver no array de exceptions nem nos hiddens da mesma coluna
                if( (!$coluna->get('nofillable', false) &&
                        !$coluna->get('nofill', false)) &&
                    !empty($nome) &&
                    !in_array($nome, $this->exceptions) &&
                    !in_array($nome, $this->default_timestamps) &&
                    (!isset($this->models[$t]['hiddens']) || !in_array($nome, $this->models[$t]['hiddens']))
                ){
                    $this->models[$t]['fillables'][] = $nome;
                }

                if($nome == "deleted_at"){
                    $this->models[$t]["hasSoftDelete"] = true;
                }else{
                    $this->models[$t]["hasSoftDelete"] = false;
                }
            }

        }

        //$this->artisan->comment(var_dump($this->fillables));
    }

    /**
     * Define as colunas com a opção hidden
     */
    public function setType($type = 'hiddens', $regras = ['hiddenfield', 'hidden']){
        $this->artisan->comment("Buscando ".$type);

        //Passa tabela a tabela
        foreach ($this->tables as $t => $table) {

            $this->artisan->comment("Tabela: ".$t);

            if(!isset($this->models[$t]) || !is_array($this->models[$t]))
                $this->models[$t] = [];

            if(!isset($this->models[$t][$type]) || !is_array($this->models[$t][$type]))
                $this->models[$t][$type] = [];

            $colunas = $table->getColumns();

            //Passa coluna a coluna
            foreach ($colunas as $c => $coluna) {

                $nome = $coluna->get('name', "");

                //Adiciona a lista se continver hiddenfield e hidden, tiver um nome e este nome nao estiver no array de exceptions

                $add = false;

                foreach ($regras as $regra)
                {
                    if($coluna->get($regra, false) || $coluna->get("type", "") == $regra)
                        $add = true;
                }

                if($add && !empty($nome) && !in_array($nome, $this->exceptions)){
                    $this->models[$t][$type][] = $nome;
                }
            }

        }

        //$this->artisan->comment(var_dump($this->models));
    }



    public function setTemplate($name)
    {
        $template = $this->template();

        $model = $this->models[$name];

        $migration = $this->migrations[$name];


        $classe = (isset($migration->model)) ? $migration->model : Str::singular(ucfirst($migration->name));

        foreach ($model as $k => $tipo) {
            $template = str_replace('%' . $k . '%', $this->ArrayString($tipo), $template);
        }

        $template = str_replace('%ClassName%', $classe, $template);

        $softDelete = ($model["hasSoftDelete"]) ? "use SoftDeletes;" : "";
        $softDeletePath = ($model["hasSoftDelete"]) ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;" : "";


        $template = str_replace('%SoftDelete%', $softDelete, $template);
        $template = str_replace('%SoftDeletePath%', $softDeletePath, $template);


        if (strtolower($classe) == "user") {
            $template = str_replace('%Extends%', "Authenticatable", $template);
            $template = str_replace('%ExtendsPath%', "use Illuminate\Foundation\Auth\User as Authenticatable;", $template);
        } else {

            $template = str_replace('%Extends%', "EloquentModelBase", $template);
            $template = str_replace('%ExtendsPath%', "use ArtisanBR\Adminx\Common\App\Models\ModelBases\EloquentModelBase;", $template);
        }
        return $template;
    }

    public function setModelTemplate($name){
        $template = $this->modelTemplate();

        $migration = $this->migrations[$name];

        $classe = (isset($migration->model)) ? $migration->model : Str::singular(ucfirst($migration->name));

        $template = str_replace('%TableName%', $migration->name, $template);

        $template = str_replace('%ClassName%', $classe, $template);

        return $template;
    }

    private function template(){
        $template = '<?php
 namespace  ArtisanBR\Adminx\Common\App\Models\Properties;

 %SoftDeletePath%
 %ExtendsPath%

 class %ClassName%Properties extends %Extends% {

     %SoftDelete%

     protected $fillable = %fillables%;

     protected $hidden = %hiddens%;

     public $timestamps = %timestamps%;

     protected $dates = %dates%;
 }';

        return $template;
    }

    public function modelTemplate(){
        return '<?php

namespace ArtisanBR\Adminx\Common\App\Models;

 use ArtisanBR\Adminx\Common\App\Models\Properties\%ClassName%Properties;

class %ClassName% extends %ClassName%Properties
{
    protected $table = "%TableName%";

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
    }

}
';
    }

    function ArrayString($array){
        return (is_array($array) && count($array) > 0) ? "['" . implode("','", $array) . "']" : "[]";
    }

    /**
     * Adicionar exceção a regra
     * @param $exception
     */
    public function addException($exception){
        if(is_array($exception))
            $this->exceptions = array_merge($this->exceptions, $exception);
        else
            $this->exceptions[] = $exception;
    }

    /**
     * Definir exceções a regra
     * @param $exception
     */
    public function setExceptions($exceptions){
        $this->exceptions = $exceptions;
    }

    /**
     * Preparar o Blueprint de todas as Migration Files e colocar em this->tables
     */
    public function blueprint(){

        $this->artisan->comment("Preparando Blueprint");

        //require nas Migration Files
        $this->migrator->requireFiles($this->files);

        //Passa nas Migration Files
        foreach ($this->files as $file) {
            $file = $this->migrator->getMigrationName($file);

            // First we will resolve a "real" instance of the migration class from this
            // migration file name. Once we have the instances we can run the actual
            // command such as "up" or "down", or we can just simulate the action.
            $migration = $this->migrator->resolve($file);


            if(isset($migration->name) && method_exists($migration, 'table')){

                //Gera o BP atravez da Migration File
                $blueprint = new Blueprint($migration->name);

                $blueprint->create();

                $callback = $migration->table();

                $callback($blueprint);

                $name = (isset($migration->model)) ? $migration->model : Str::singular($migration->name);

                $this->tables[$name] = $blueprint;
                $this->migrations[$name] = $migration;
            }

        }
    }
}
