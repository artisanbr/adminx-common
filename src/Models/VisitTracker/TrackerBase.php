<?php

namespace Adminx\Common\Models\VisitTracker;


use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use PragmaRX\Tracker\Vendor\Laravel\Models\Base;

abstract class TrackerBase extends Base
{
    use Cachable;

}