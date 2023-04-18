<?php

namespace Adminx\Common\Enums\Forms;

enum FormElementType: string
{
    //FIELDS
    case HiddenField = 'hidden_field';
    case TextField = 'text_field';
    case TextArea = 'text_area';
    case Checkbox = 'checkbox';
    case RadioButton = 'radio_button';
    case SelectList = 'select_list';
    case FileField = 'file_field';
    case HtmlField = 'html_field';
    //todo: file, date, hour, password (encrypt), recaptcha (site config)
    //CUSTOM
    case Html = 'html';
    case Text = 'text';


    public function _hasUpload(): bool
    {
        return self::hasUpload($this);
    }

    public static function hasUpload($type): bool
    {
        return match ($type) {
            self::FileField => true,
            default => false,
        };
    }

    public function _title(): string
    {
        return self::title($this);
    }

    public static function title($type): string
    {
        return match ($type) {
            self::TextField => 'Campo de Texto',
            self::TextArea => 'Área de Texto (textarea)',
            self::Checkbox => 'Caixa de Seleção',
            self::RadioButton => 'Botão de Rádio',
            self::SelectList => 'Lista de Seleção',
            self::HiddenField => 'Campo/Valor Oculto',
            self::FileField => 'Envio de Arquivo',
            self::HtmlField => 'Campo HTML (em breve)',
            self::Html => 'HTML Personalizado',
            self::Text => 'Texto Personalizado',
        };
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            function (self $item) {
                return $item->_title();
            },
            self::cases()
        ));
    }

    public function _tag(): string
    {
        return self::tag($this);
    }

    public static function tag($type): string
    {
        return match ($type) {
            self::HiddenField, self::TextField, self::RadioButton, self::Checkbox => 'input',
            self::TextArea => 'textarea',
            self::SelectList => 'select',
            self::HtmlField => 'editor',
            self::Html => 'div',
        };
    }

    public static function tags(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            function (self $item) {
                return $item->_tag();
            },
            self::cases()
        ));
    }

    public function _icon(): string
    {
        return self::icon($this);
    }

    public static function icon($type): string
    {
        return match ($type) {
            self::TextField => 'bi bi-input-cursor-text',
            self::TextArea => 'bi bi-textarea-t',
            self::Checkbox => 'bi bi-ui-checks',
            self::RadioButton => 'bi bi-ui-radios',
            self::SelectList => 'bi bi-menu-button-wide-fill',
            self::HiddenField => 'bi bi-eye-slash',
            self::HtmlField => 'bi bi-code-square',
            self::Html => 'bi bi-code-slash',
        };
    }

    public static function icons(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            function (self $item) {
                return $item->_icon();
            },
            self::cases()
        ));
    }

    public function _possibleAttributes(): array
    {
        return self::possibleAttributes($this);
    }

    public static function possibleAttributes($type): array
    {
        return match ($type) {
            self::TextField, self::RadioButton, self::Checkbox, self::TextArea, self::HtmlField => ['required'],
            self::SelectList => ['required', 'multiple'],
            default => [],
        };
    }

    public static function allPossibleAttributes(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            function (self $item) {
                return $item->_possibleAttributes();
            },
            self::cases()
        ));
    }

    public static function attributePossibleTypes($attribute): array
    {
        $types = [];
        foreach (self::cases() as $type) {
            if (collect($type->_possibleAttributes())->contains($attribute)) {
                $types[] = $type->name;
            }
        }

        return $types;
    }

    public static function typeGroups(): array
    {
        return [
            'Formulários' => [
                self::TextField,
                self::TextArea,
                self::Checkbox,
                self::RadioButton,
                self::SelectList,
                self::HtmlField,
                self::HiddenField,
            ],
            'Conteúdo' => [
                self::Html,
            ],
        ];
    }

    public static function typeGroup($type)
    {
        return collect(self::typeGroups())->search($type);
    }

    public function _typeGroup()
    {
        return collect(self::typeGroups())->search($this->name);
    }
}
