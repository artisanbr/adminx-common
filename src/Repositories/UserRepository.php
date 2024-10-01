<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories;

use Adminx\Common\Facades\FileManager\FileUpload;
use Adminx\Common\Models\Users\AccessRules\Role;
use Adminx\Common\Models\Users\User;
use Adminx\Common\Repositories\Base\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

/**
 * @property ?User $model
 */
class UserRepository extends Repository
{
    protected string $modelClass = User::class;

    public function __construct(
        protected $clientRepository = new ClientRepository(),
    ) {}

    public static function personalAcessClientName(): string
    {
        return config('app.name') . ' Personal Access Client';
    }

    public static function passwordGrantClientName(): string
    {
        return config('app.name') . ' Password Grant Client';
    }

    public function user(User|int $user): static
    {
        $this->setModel($user);

        return $this;
    }

    public function saveTransaction(): ?User
    {

        $this->model->fill($this->data);
        $this->model->save();

        $this->processUploads();

        $this->model->sites()->sync($this->data['sites'] ?? []);

        Artisan::call('passport:install');

        //Permissions
        if(Auth::user()->can(['update permission'])){

            if($this->model->config->custom_permissions){

                //Permissões Customizadas
                //$this->model->syncRoles([]);
                $this->model->syncPermissions($this->data['permissions_list'] ?? []);

            }else{
                $this->model->syncPermissions([]);
            }

            if($this->data['roles'] ?? false){

                //Grupo de Permissões
                $selected_roles = Role::whereIn("id", array_values($this->data['roles']))->get();
                $this->model->syncRoles($selected_roles);


            }else if(!($this->data['id'] ?? false)){

                //Novo usuário
                $this->model->syncPermissions([]);
                $this->model->syncRoles(['guest']);
            }else{
                $this->model->syncPermissions($this->data['permissions_list'] ?? []);
                $this->model->syncRoles([]);
            }
        }

        Artisan::call("permission:cache-reset");

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

        if (!$this->model) {
            abort(404, 'Usuário não encontrado');
        }

        $uploadFile = $this->data['avatar_file'] ?? false;

        //Avatar
        if ($uploadFile) {


            $this->model->refresh();

            $this->uploadPathBase = $this->model->uploadPathTo('images');


            $avatarFile = FileUpload::upload($uploadFile, $this->uploadPathBase, 'avatar');

            if ($avatarFile) {
                $this->model->avatar_url = $avatarFile->url;

                $this->model->save();
                $this->model->refresh();
            }

        }

    }

    public function prepareAuthentication(User|int $user = null): bool|Client
    {

        if ($user) {
            $this->user($user);
        }

        if ($this->user) {
            $this->clientRepository->createPasswordGrantClient($this->user->id, self::passwordGrantClientName(), config('app.url'), 'users');
            $this->clientRepository->createPersonalAccessClient($this->user->id, self::personalAcessClientName(), config('app.url'));
        }

        return false;
    }
}
