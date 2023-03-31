<?php

namespace ArtisanBR\Adminx\Common\App\Models\Interfaces;

use ArtisanBR\Adminx\Common\App\Models\Site;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use Delight\Random\Random;

/**
 * @property int $site_id
 * @property int $user_id
 * @property int $account_id
 * @method void defineOwners(array|string $owner_type = ['user', 'site', 'account'])
 * @property Site|null $site
 */
interface OwneredModel
{
}
