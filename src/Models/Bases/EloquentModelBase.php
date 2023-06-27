<?php

namespace Adminx\Common\Models\Bases;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentModelBase extends Model
{

    public $timestamps = true;

    protected array $ownerTypes = ['user', 'site', 'account'];
    protected string $htmlCacheAttribute = 'html';
    protected string $htmlRawAttribute = 'html_raw';

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
