<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Interfaces;

use Adminx\Common\Models\Sites\Site;

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
