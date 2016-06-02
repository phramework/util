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
class Request
{
    /**
     * Check if incoming request is send using ajax
     * @return bool
     */
    public static function isAjaxRequest() : bool
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check if incoming request is send using HTTPS protocol
     * @return boolean
     */
    public static function isHTTPS() : bool
    {
        return (isset($_SERVER['HTTPS'])
            && ($_SERVER['HTTPS'] === 'on'))
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        );
    }

    /**
     * Get the ip address of the client
     * @return string|null Returns fails on failure
     */
    public static function getIPAddress()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            ///to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return null;
    }
}
