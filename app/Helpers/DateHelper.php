<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function formatDateIndonesian($date)
    {
        if (!$date) {
            return '';
        }

        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        $monthName = $months[$carbonDate->month - 1];

        return $carbonDate->day . ' ' . $monthName . ' ' . $carbonDate->year;
    }
}
