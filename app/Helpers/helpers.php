<?php

declare(strict_types=1);

if (!function_exists('format_number')) {
    function format_number(int|float $number): string
    {
        if ($number >= 1_000_000) {
            return round($number / 1_000_000, 1) . 'M';
        }
        if ($number >= 1_000) {
            return round($number / 1_000, 1) . 'K';
        }
        return (string) number_format((float) $number);
    }
}

if (!function_exists('safe_domain')) {
    function safe_domain(?string $url): string
    {
        if (empty($url)) {
            return '-';
        }
        $host = parse_url($url, PHP_URL_HOST);
        return $host ?: $url;
    }
}

