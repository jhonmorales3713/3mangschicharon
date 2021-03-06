<?php

$snakeCache = [];
$camelCache = [];
$studlyCache = [];

if (! function_exists('camel_case')) {
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel_case($value)
    {
        if (isset($camelCache[$value])) {
            return $camelCache[$value];
        }

        return $camelCache[$value] = lcfirst(studly_case($value));
    }
}

if (! function_exists('ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function ends_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('kebab_case')) {
    /**
     * Convert a string to kebab case.
     *
     * @param  string  $value
     * @return string
     */
    function kebab_case($value)
    {
        return snake_case($value, '-');
    }
}

if (! function_exists('preg_replace_array')) {
    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param  string  $pattern
     * @param  array   $replacements
     * @param  string  $subject
     * @return string
     */
    function preg_replace_array($pattern, array $replacements, $subject)
    {
        return preg_replace_callback($pattern, function () use (&$replacements) {
            foreach ($replacements as $key => $value) {
                return array_shift($replacements);
            }
        }, $subject);
    }
}

if (! function_exists('snake_case')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        $key = $value;

        if (isset($snakeCache[$key][$delimiter])) {
            return $snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = str_lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return $snakeCache[$key][$delimiter] = $value;
    }
}

if (! function_exists('starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('str_after')) {
    /**
     * Return the remainder of a string after a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function str_after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}

if (! function_exists('str_before')) {
    /**
     * Get the portion of a string before a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function str_before($subject, $search)
    {
        return $search === '' ? $subject : explode($search, $subject)[0];
    }
}

if (! function_exists('str_contains')) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('str_finish')) {
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    function str_finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }
}

if (! function_exists('str_is')) {
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string|array  $pattern
     * @param  string  $value
     * @return bool
     */
    function str_is($pattern, $value)
    {
        $patterns = str_wrap($pattern);

        if (empty($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern) {
            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }
            $pattern = preg_quote($pattern, '#');
            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);
            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }
}

if (! function_exists('str_wrap')) {
    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    function str_wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return ! is_array($value) ? [$value] : $value;
    }
}

if (! function_exists('str_random')) {
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
    function str_random($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}

if (! function_exists('str_replace_array')) {
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array   $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = str_replace_first($search, $value, $subject);
        }

        return $subject;
    }
}

if (! function_exists('str_replace_first')) {
    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_first($search, $replace, $subject)
    {
        if ($search === '') {
            return $subject;
        }
        $position = strpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
}

if (! function_exists('str_replace_last')) {
    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_last($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
}

if (! function_exists('str_slug')) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string  $language
     * @return string
     */
    function str_slug($title, $separator = '-', $language = 'en')
    {
        $title = str_ascii($title, $language);
        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
        // Replace @ with the word 'at'
        $title = str_replace('@', $separator.'at'.$separator, $title);
        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}

if (! function_exists('str_start')) {
    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $prefix
     * @return string
     */
    function str_start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }
}

if (! function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly_case($value)
    {
        $key = $value;
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}

if (! function_exists('title_case')) {
    /**
     * Convert a value to title case.
     *
     * @param  string  $value
     * @return string
     */
    function title_case($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}

if (! function_exists('str_lower')) {
    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    function str_lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }
}

if (! function_exists('str_ascii')) {
    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param  string  $value
     * @param  string  $language
     * @return string
     */
    function str_ascii($value, $language = 'en')
    {
        $languageSpecific = languageSpecificCharsArray($language);

        if (! is_null($languageSpecific)) {
            $value = str_replace($languageSpecific[0], $languageSpecific[1], $value);
        }

        foreach (charsArray() as $key => $val) {
            $value = str_replace($val, $key, $value);
        }

        return preg_replace('/[^\x20-\x7E]/u', '', $value);
    }
}

if (! function_exists('charsArray')) {
    /**
     * Returns the replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     *
     * @return array
     */
    function charsArray()
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            '0' => ['??', '???', '??', '???'],
            '1' => ['??', '???', '??', '???'],
            '2' => ['??', '???', '??', '???'],
            '3' => ['??', '???', '??', '???'],
            '4' => ['???', '???', '??', '??', '???'],
            '5' => ['???', '???', '??', '??', '???'],
            '6' => ['???', '???', '??', '??', '???'],
            '7' => ['???', '???', '??', '???'],
            '8' => ['???', '???', '??', '???'],
            '9' => ['???', '???', '??', '???'],
            'a' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '???', '???', '???', '??', '??', '??', '???', '???', '??', '???', '??'],
            'b' => ['??', '??', '??', '???', '???', '???'],
            'c' => ['??', '??', '??', '??', '??', '???'],
            'd' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '???', '???', '???', '???'],
            'e' => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '??', '??', '???'],
            'f' => ['??', '??', '??', '??', '???', '???'],
            'g' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???'],
            'h' => ['??', '??', '??', '??', '??', '??', '???', '???', '???', '???'],
            'i' => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '??', '???', '???', '??', '??', '??', '???', '???', '???', '??????', '??', '???', '???', '??', '???'],
            'j' => ['??', '??', '??', '???', '??', '???'],
            'k' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '???'],
            'l' => ['??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'm' => ['??', '??', '??', '???', '???', '???'],
            'n' => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'o' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??????', '??', '??', '??', '???', '???', '???', '??'],
            'p' => ['??', '??', '???', '???', '??', '???'],
            'q' => ['???', '???'],
            'r' => ['??', '??', '??', '??', '??', '??', '???', '???'],
            's' => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '???', '???'],
            't' => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???', '???', '???'],
            'u' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??'],
            'v' => ['??', '???', '??', '???'],
            'w' => ['??', '??', '??', '???', '???', '???'],
            'x' => ['??', '??', '???'],
            'y' => ['??', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'z' => ['??', '??', '??', '??', '??', '??', '???', '???', '???'],
            'aa' => ['??', '???', '??'],
            'ae' => ['??', '??'],
            'ai' => ['???'],
            'ch' => ['??', '???', '???', '??'],
            'dj' => ['??', '??'],
            'dz' => ['??', '???'],
            'ei' => ['???'],
            'gh' => ['??', '???'],
            'ii' => ['???'],
            'ij' => ['??'],
            'kh' => ['??', '??', '???'],
            'lj' => ['??'],
            'nj' => ['??'],
            'oe' => ['??', '??', '??'],
            'oi' => ['???'],
            'oii' => ['???'],
            'ps' => ['??'],
            'sh' => ['??', '???', '??'],
            'shch' => ['??'],
            'ss' => ['??'],
            'sx' => ['??'],
            'th' => ['??', '??', '??', '??', '??'],
            'ts' => ['??', '???', '???'],
            'ue' => ['??'],
            'uu' => ['???'],
            'ya' => ['??'],
            'yu' => ['??'],
            'zh' => ['??', '???', '??'],
            '(c)' => ['??'],
            'A' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '???', '??'],
            'B' => ['??', '??', '???', '???'],
            'C' => ['??', '??', '??', '??', '??', '???'],
            'D' => ['??', '??', '??', '??', '??', '??', '???', '???', '??', '??', '???'],
            'E' => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '??', '??', '???'],
            'F' => ['??', '??', '???'],
            'G' => ['??', '??', '??', '??', '??', '??', '???'],
            'H' => ['??', '??', '??', '???'],
            'I' => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'J' => ['???'],
            'K' => ['??', '??', '???'],
            'L' => ['??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'M' => ['??', '??', '???'],
            'N' => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'O' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '??'],
            'P' => ['??', '??', '???'],
            'Q' => ['???'],
            'R' => ['??', '??', '??', '??', '??', '???'],
            'S' => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'T' => ['??', '??', '??', '??', '??', '??', '???'],
            'U' => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '??'],
            'V' => ['??', '???'],
            'W' => ['??', '??', '??', '???'],
            'X' => ['??', '??', '???'],
            'Y' => ['??', '???', '???', '???', '???', '??', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'Z' => ['??', '??', '??', '??', '??', '???'],
            'AE' => ['??', '??'],
            'Ch' => ['??'],
            'Dj' => ['??'],
            'Dz' => ['??'],
            'Gx' => ['??'],
            'Hx' => ['??'],
            'Ij' => ['??'],
            'Jx' => ['??'],
            'Kh' => ['??'],
            'Lj' => ['??'],
            'Nj' => ['??'],
            'Oe' => ['??'],
            'Ps' => ['??'],
            'Sh' => ['??'],
            'Shch' => ['??'],
            'Ss' => ['???'],
            'Th' => ['??'],
            'Ts' => ['??'],
            'Ya' => ['??'],
            'Yu' => ['??'],
            'Zh' => ['??'],
            ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", "\xEF\xBE\xA0"],
        ];
    }
}

if (! function_exists('languageSpecificCharsArray')) {
    /**
     * Returns the language specific replacements for the ascii method.
     *
     * Note: Adapted from Stringy\Stringy.
     *
     * @see https://github.com/danielstjules/Stringy/blob/3.1.0/LICENSE.txt
     *
     * @param  string  $language
     * @return array|null
     */
    function languageSpecificCharsArray($language)
    {
        static $languageSpecific;

        if (! isset($languageSpecific)) {
            $languageSpecific = [
                'bg' => [
                    ['??', '??', '??', '??', '??', '??', '??', '??'],
                    ['h', 'H', 'sht', 'SHT', 'a', '??', 'y', 'Y'],
                ],
                'de' => [
                    ['??',  '??',  '??',  '??',  '??',  '??'],
                    ['ae', 'oe', 'ue', 'AE', 'OE', 'UE'],
                ],
            ];
        }

        return $languageSpecific[$language] ?? null;
    }
}
