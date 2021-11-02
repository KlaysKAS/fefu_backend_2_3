<?php

namespace App\Sanitizers;

class PhoneSanitizer
{
    /**
     * @param string $number
     * @return string
     */

    public static function sanitize(string $number): string
    {
        $returned = preg_replace('/\D+/', '', $number);
        $returned[0] = '7';
        return $returned;
    }
}
