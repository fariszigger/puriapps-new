<?php
use Illuminate\Support\Carbon;

if (!function_exists('formatIndonesianDate')) {
    /**
     * Format a Carbon date or date string to an Indonesian string format
     *
     * @param \Carbon\Carbon|string|null $date
     * @return string
     */
    function formatIndonesianDate($date)
    {
        if (!$date) return '____';
        
        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $carbonDate->format('d') . ' ' . $months[(int)$carbonDate->format('m')] . ' ' . $carbonDate->format('Y');
    }
}
