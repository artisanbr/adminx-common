<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSelect2
{
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->title ?? '',
        );
    }

    protected function optionList(): Attribute {
        return Attribute::make(
            get: fn() => [
                $this->id => $this->text
            ]
        );
    }

    protected function selectOptionList(): Attribute {
        return $this->optionList();
    }

    public function setSelected($selected_value): static
    {
        if($selected_value && (string) $this->id === (string) $selected_value) {
            $this->selected = true;
        }

        return $this;
    }
}
