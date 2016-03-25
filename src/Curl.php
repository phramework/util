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
class Curl
{
    /**
     * Get Headers from a remote link
     * @param string $url
     * @return array Return headers
     */
    public static function curlHeaders($url, &$data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        return $headers;
    }

    /**
     * Download a file from a remote link
     * @param string $url
     * @param string $path
     * @return bool True if download succeed
     */
    public static function curlDownload($url, $path, $timeout = 3600)
    {
        $return = false;
        try {
            // open file to write
            $fp = fopen($path, 'w+');
            // start curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // set return transfer to false
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // increase timeout to download big file
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            // write data to local file
            curl_setopt($ch, CURLOPT_FILE, $fp);
            // execute curl
            $return = curl_exec($ch);
            // close curl
            curl_close($ch);
            // close local file
            fclose($fp);
        } catch (\Exception $e) {
            return false;
        }
        return $return;
    }
}