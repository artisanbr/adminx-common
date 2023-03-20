<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Forms;

use Illuminate\Support\Facades\Auth;
use ArtisanLabs\GModel\GenericModel;

class FormSendButton extends GenericModel
{

    protected $fillable = [
        'text',
        'icon',
        'attrs',
        //todo: js events, messagens de retorno,
    ];

    protected $attributes = [
        'text' => 'Enviar',
        'icon' => '<i class="fa-solid fa-paper-plane"></i>',
        'attrs' => [
            'class' => 'primary_btn btn btn-primary btn-icon ml-0 ms-auto me-0'
        ],
    ];

    protected $casts = [
        'text' => 'string',
        'icon' => 'string',
        'attrs' => 'collection',
    ];

    protected $appends = [
        'render_html_attributes',
        'html',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected function getRenderHtmlAttributesAttribute()
    {
        return $this->attrs->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getHtmlAttribute()
    {
        return "<button type='submit' {$this->render_html_attributes}>
                    <span>{$this->icon}</span> {$this->text}
                </button>";
    }
}
