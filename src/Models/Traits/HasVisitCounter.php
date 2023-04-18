<?php

namespace Adminx\Common\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

trait HasVisitCounter
{

    public function uniqueVisits(){
        return $this->visitLogs()->distinct('ip');
    }

    protected function visitsCount(): Attribute {
        return Attribute::make(get: fn() => $this->visitLogs()->count());
    }

    protected function uniqueVisitsCount(): Attribute {
        return Attribute::make(get: fn() => $this->uniqueVisits()->count('ip'));
    }

    protected function monthUniqueVisitsCount(): Attribute {
        return Attribute::make(get: fn() => $this->uniqueVisits()->whereMonth('created_at', Carbon::today()->month)->whereYear('created_at', date('Y'))->count('ip'));
    }
    protected function weekUniqueVisitsCount(): Attribute {
        return Attribute::make(get: fn() => $this->uniqueVisits()->whereBetween('created_at', [Carbon::today()->startOfWeek(), Carbon::today()])->count('ip'));
    }

    protected function dayUniqueVisitsCount(): Attribute {
        return Attribute::make(get: fn() => $this->uniqueVisits()->whereDate('created_at', Carbon::today())->count('ip'));
    }



}
