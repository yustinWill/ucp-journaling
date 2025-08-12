<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateTime
{
    public function getLastMonthEndOfMonth($dateString)
    {
        $date = Carbon::parse($dateString);
        return $date->subMonthNoOverflow()->endOfMonth()->toDateString();
    }

}
