<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config\Import;

use ArtisanLabs\GModel\GenericModel;
use Corcel\Model\Post;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class SiteImportConfig extends GenericModel
{

    protected $fillable = [
        'wordpress',
    ];

    protected $attributes = [
        /*'wordpress' => [

        ],*/
    ];

    protected $casts = [
        'wordpress' => WordpressImportConfig::class,
    ];

    public function checkConnection()
    {

        if ($this->database && $this->username && $this->password) {
            try {

                Config::set('database.connections.corcel', [
                    ...config('database.connections.corcel'),
                    ...$this->toArray(),
                ]);

                $postsCheck = Post::count();

                if ($postsCheck) {
                    $this->checked = true;
                }
                else {
                    $this->checked = false;
                }

            } catch (Exception $e) {
                $this->checked = false;
            }
        }
        else {
            $this->checked = false;
        }

        return $this->checked;
    }

    public function lockPassword(): ?string
    {
        if (!empty($this->password ?? null)) {
            $this->password = Crypt::encrypt($this->password);
        }

        return $this->password;
    }

    public function password_decrypt(): string
    {
        return !empty($this->password ?? null) ? Crypt::decrypt($this->password) : '';
    }
    
}
