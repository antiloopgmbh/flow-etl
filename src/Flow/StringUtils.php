<?php

namespace Flow;

abstract class StringUtils
{
    public static function str_starts_with($haystack, $needle): bool
    {
        $length = strlen( $needle );

        return substr( $haystack, 0, $length ) === $needle;
    }

    public static function str_ends_with($haystack, $needle): bool
    {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }

        return substr( $haystack, -$length ) === $needle;
    }
}