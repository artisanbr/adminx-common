<?php

namespace Adminx\Common\Models\Interfaces;

use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Delight\Random\Random;

/**
 * @property int $id
 * @property string $public_id
 */
interface PublicIdModel
{
    public function generatePublicId(): string;

    public function renewPublicId(): void;
}
