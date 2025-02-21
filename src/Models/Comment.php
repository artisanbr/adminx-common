<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;

class Comment extends EloquentModelBase
{
    use BelongsToSite, BelongsToUser, HasMorphAssigns, HasValidation;

    protected $fillable = [
        'site_id',
        'user_id',
        'approved',
        'name',
        'email',
        'comment',
        'ip',
        'commentable_id',
        'commentable_type',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'name' => 'string',
        'email' => 'string',
        'comment' => 'string',
        'ip' => 'string',
    ];

    protected $appends = [
        'gravatar',
    ];


    //region VALIDATIONS
    public static function createRules(?FormRequest $request = null): array {
        return [
            'public_id' => ['required'],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'comment' => ['required'],
        ];
    }
    //endregion

    //region HELPERS
    public function customGravatar($size = 100): ?string
    {
        return empty($this->email) ? null : '//www.gravatar.com/avatar/'.md5($this->email).'fs='.$size;
    }
    //endregion

    //region ATTRIBUTES
    protected function gravatar(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->customGravatar()
        );
    }
    protected function comment(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: fn($value) => nl2br($value),
        );
    }
    //endregion

    //region OVERRIDES
    /**
     * @throws FrontendException
     */
    public function save(array $options = []): bool
    {
        if($this->ip && self::whereSiteId($this->site_id)->whereIp($this->ip)->whereDate('created_at', Carbon::today())->count() > 2){
            throw new FrontendException('Parece que você já comentou algumas vezes hoje, tente novamente outro dia, evite spams.', 401);
        }

        return parent::save($options); // TODO: Change the autogenerated stub
    }
    //endregion

    //region RELATIONS
    public function commentable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__.'_type', __FUNCTION__.'_id');
    }
    //endregion
}
