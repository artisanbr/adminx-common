<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\Helpers\DateTimeHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPublishTimestamps
{
    protected $dates = ['published_at', 'unpublished_at'];

    protected function setUnpublishAttribute($value): static
    {
        if ($value) {
            $this->unpublished_at = Carbon::now();
        }

        return $this;

    }

    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value) : $this->created_at,
            set: fn($value) => DateTimeHelper::DBTrait($value),
        );

    }

    protected function unpublishedAtAt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => DateTimeHelper::DBTrait($value),
        );

    }


    /*protected function setPublishedAtAttribute($value): static
    {
        $this->attributes['published_at'] = DateTimeHelper::DBTrait($value);

        return $this;

    }*/

    /*protected function setUnpublishedAtAttribute($value): static
    {
        $this->attributes['unpublished_at'] = DateTimeHelper::DBTrait($value);

        return $this;
    }*/
}
