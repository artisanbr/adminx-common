<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Sites\Enums\SiteRouteType;
use Adminx\Common\Models\Sites\Objects\Config\SiteRouteConfig;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SiteRoute extends EloquentModelBase
{
    use BelongsToSite, BelongsToPage, HasOwners;

    protected array $ownerTypes = ['site'];

    protected $table = 'site_routes';

    public $incrementing = true;

    protected $fillable = [
        'site_id',
        'page_id',
        'url',
        'type',
        'external',
        'canonical',
        'model_type',
        'model_id',
        'config',
    ];

    protected $casts = [
        'url'        => 'string',
        'type'       => SiteRouteType::class,
        'external'   => 'boolean',
        'canonical'  => 'boolean',
        'config'     => SiteRouteConfig::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];

    //region Relations
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function related_page(): BelongsTo
    {
        return $this->page();
    }
    //endregion


}
