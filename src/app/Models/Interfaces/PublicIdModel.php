<?php

namespace ArtisanBR\Adminx\Common\App\Models\Interfaces;

use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
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
