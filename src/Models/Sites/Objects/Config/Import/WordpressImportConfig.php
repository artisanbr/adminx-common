<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config\Import;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Tools\Facades\WordpressImport;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Facades\Crypt;

class WordpressImportConfig extends GenericModel
{

    protected $fillable = [
        'checked',
        'host',
        'database',
        'username',
        'password',
        'prefix',
        'uri',
        'media_ftp',
    ];

    protected $attributes = [
        'checked'  => false,
        'host'     => 'localhost',
        'database' => 'fvconsultoria_wp',
        'username' => 'fvconsultoria_wp',
        'password' => 'ROGsTKHJFEGq',
        'prefix'   => 'wp_',
        'uri'      => 'https://fvconsultoriaetreinamento.com/',
    ];

    protected $casts = [
        'checked'   => 'bool',
        'host'      => 'string',
        'database'  => 'string',
        'password'  => 'string',
        'username'  => 'string',
        'prefix'    => 'string',
        'uri'       => 'string',
        'media_ftp' => FtpMediaImportConfig::class,
    ];

    public function checkConnection(): ?bool
    {
        $this->checked = WordpressImport::setConfig($this)->checkConnection();

        return $this->checked;
    }

    public function loadUri()
    {

        $this->attributes['uri'] = WordpressImport::setConfig($this)->getWordpressUri();

        return$this->uri;
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

    //region Attributes
    //region GET's
    protected function getUriAttribute()
    {
        //$uri = $this->attributes["uri"] ?? '';
        $uri = $this->attributes["uri"] ?? '';

        return Str::endsWith($uri, '/') ? $uri : "{$uri}/";
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion
}
