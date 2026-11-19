<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Waktu disimpan di database dalam UTC (config app.timezone); tampilan ke admin memakai zona waktu setempat
 * (config app.display_timezone).
 */
class DateHelper
{
    /** Salinan waktu dalam zona waktu tampilan, berbahasa Indonesia, siap diformat. */
    public static function local(CarbonInterface $time): Carbon
    {
        return Carbon::instance($time)->timezone(config('app.display_timezone'))->locale('id');
    }

    /** Sekarang menurut zona waktu tampilan (untuk "hari ini" pada penyaring tanggal). */
    public static function today(): Carbon
    {
        return Carbon::now(config('app.display_timezone'));
    }

    /** Awal atau akhir hari (menurut zona waktu tampilan) dari teks tanggal, dalam UTC agar bisa dibandingkan dengan kolom database. */
    public static function dayBoundary(string $date, bool $end = false): Carbon
    {
        $day = Carbon::parse($date, config('app.display_timezone'));

        return ($end ? $day->endOfDay() : $day->startOfDay())->utc();
    }
}
