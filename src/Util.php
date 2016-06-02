<?php
/**
 * Copyright 2016 Xenofon Spafaridis
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Phramework\Util;

/**
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Xenofon Spafaridis <nohponex@gmail.com>
 * @since 0.0.0
 */
class Util
{
    /**
     * Check if given string is valid JSON string
     * @param  string  $string
     * @return boolean
     * @note In php 7.0 json_decode has errors when string is empty
     */
    public static function isJSON(string $string) : bool
    {
        if (empty($string)) {
            return false;
        }

        $object = json_decode($string);

        return is_string($string) && json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Clears all non ASCII characters from a string and replaces /,_,|,+, ,- characters to '-'
     * @param string $str The input string
     * @return string Returns the clean string
     */
    public static function toAscii(string $str) : string
    {
        $clean = preg_replace('/[^a-zA-Z0-9\.\/_|+ -]/', '', $str);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace('/[\/_|+ -]+/', '-', $clean);

        return $clean;
    }

    public static function dateFormatted($datetime, string $format = 'j M Y G:i') : string
    {
        $date = new DateTime($datetime);
        return $date->format($format);
    }

    /**
     * Generate a random 40 character hex token
     * @return string Returns a random token
     */
    public static function token($prefix = '') : string
    {
        $token = sha1(uniqid($prefix, true) . mt_rand());

        return $token;
    }
    /**
     * Create a random readable word
     * @param  integer $length *[Optional]* String's length
     * @return string
     */
    public static function readableRandomString($length = 8) : string
    {
        $consonants = [
            'b',
            'c',
            'd',
            'f',
            'g',
            'h',
            'j',
            'k',
            'l',
            'm',
            'n',
            'p',
            'r',
            's',
            't',
            'v',
            'w',
            'x',
            'y',
            'z'
        ];

        $vowels = ['a', 'e', 'i', 'o', 'u'];

        $word = '';
        srand((double) microtime() * 1000000);
        $max = $length / 2;

        for ($i = 1; $i <= $max; ++$i) {
            $word .= $consonants[rand(0, count($consonants)-1)];
            $word .= $vowels[rand(0, count($vowels)-1)];
        }

        if (strlen($word) < $length) {
            $word .= $vowels[rand(0, count($vowels)-1)];
        }

        return $word;
    }
    
    /**
     * Generate UUID
     * @return string Returns a 36 characters string
     * @since 1.2.0
     */
    public static function generateUUID() :string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param array $array
     * @param string $type
     * @return bool
     */
    public static function isArrayOf(array $array, string $type = 'string') : bool
    {
        foreach ($array as $value) {
            $valueType = gettype($value);

            if ($type !== $valueType && !(is_object($value) && $value instanceof $type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $array
     * @return bool
     */
    public static function isArrayAssoc(array $array) : bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Cartesian product
     * @param  array $input [description]
     * @return array        [description]
     * @source http://stackoverflow.com/a/6313346/2255129
     * @example
     * ```php
     * $input = [
     *     'arm' => ['A', 'B'],
     *     'gender' => ['Female', 'Male']
     * ];
     *
     * print_r(Util::cartesian($input));
     * ```
     */
    public static function cartesian(array $input) : array
    {
        $result = array();

        foreach ($input as $key => $values) {
            // If a sub-array is empty, it doesn't affect the cartesian product
            if (empty($values)) {
                continue;
            }

            // Seeding the product array with the values from the first sub-array
            if (empty($result)) {
                foreach ($values as $value) {
                    $result[] = [$key => $value];
                }
            } else {
                // Second and subsequent input sub-arrays work like this:
                //   1. In each existing array inside $product, add an item with
                //      key == $key and value == first item in input sub-array
                //   2. Then, for each remaining item in current input sub-array,
                //      add a copy of each existing array inside $product with
                //      key == $key and value == first item of input sub-array

                // Store all items to be added to $product here; adding them
                // inside the foreach will result in an infinite loop
                $append = [];

                foreach ($result as &$product) {
                    // Do step 1 above. array_shift is not the most efficient, but
                    // it allows us to iterate over the rest of the items with a
                    // simple foreach, making the code short and easy to read.
                    $product[$key] = array_shift($values);

                    // $product is by reference (that's why the key we added above
                    // will appear in the end result), so make a copy of it here
                    $copy = $product;

                    // Do step 2 above.
                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    // Undo the side effects of array_shift
                    array_unshift($values, $product[$key]);
                }

                // Out of the foreach, we can add to $results now
                $result = array_merge($result, $append);
            }
        }

        return $result;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @source http://stackoverflow.com/a/10473026/2255129
     */
    public static function startsWith(string $haystack, string $needle) : bool
    {
        // search backwards starting from haystack length characters from the end
        return (
            $needle === ''
            || strrpos($haystack, $needle, -strlen($haystack)) !== false
        );
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @source http://stackoverflow.com/a/10473026/2255129
     */
    public static function endsWith(string $haystack, string $needle) : bool
    {
        // search forward starting from end minus needle length characters
        return (
            $needle === ''
            || (
                ($temp = strlen($haystack) - strlen($needle)) >= 0
                && strpos($haystack, $needle, $temp) !== false
            )
        );
    }
}
