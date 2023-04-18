<?php

namespace Adminx\Common\Models\Interfaces;

use Adminx\Common\Models\Site;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
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
