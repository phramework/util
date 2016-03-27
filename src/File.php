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
class File
{

    /**
     * Extract extension from file's path
     * @param string $filePath The file path
     * @return string The extension without dot prefix
     */
    public static function extension($filePath)
    {
        return strtolower(preg_replace('/^.*\.([^.]+)$/D', '$1', $filePath));
    }

    /**
     * Join directories and filename to create path
     * @param array $array Array with directories and filename for example array( '/tmp', 'me', 'file.tmp' )
     * @param string $glue *[Optional]*
     * @return string Path
     */
    public static function getPath($array, $glue = DIRECTORY_SEPARATOR)
    {
        return str_replace('\\\\', '\\', join($glue, $array));
    }

    /**
     * Get the size of a file
     * Works for large files too (>2GB)
     * @param string $path File's path
     * @param double File's size
     */
    public static function getFileSize($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            if (class_exists('COM')) {
                $fsobj = new COM('Scripting.FileSystemObject');
                $f = $fsobj->GetFile(realpath($path));
                $size = $f->Size;
            } else {
                $size = trim(exec("for %F in (\"" . $path . "\") do @echo %~zF"));
            }
        } elseif (PHP_OS == 'Darwin') {
            $size = trim(shell_exec("stat -f %z " . escapeshellarg($path)));
        } elseif (in_array(PHP_OS, [ 'Linux', 'FreeBSD', 'Unix', 'SunOS'])) {
            $size = trim(shell_exec("stat -c%s " . escapeshellarg($path)));
        } else {
            $size = filesize($path);
        }

        return doubleval($size);
    }

    /**
     * Get an array that represents directory tree
     * @param string  $directory     Directory path
     * @param boolean $recursive     *[Optional]* Include sub directories
     * @param boolean $listDirs      *[Optional]* Include directories on listing
     * @param boolean $listFiles     *[Optional]* Include files on listing
     * @param string  $exclude       *[Optional]* Exclude paths that matches this
     * regular expression
     * @param array   $allowedFileTypes *[Optional]* Allowed file extensions,
     * default `[]` (allow all)
     * @param boolean $relativePath *[Optional]* Return paths in relative form,
     * default `false`
     * @link https://github.com/phramework/phramework/blob/master/src/Models/Util.php Source
     * @return array
     */
    public static function directoryToArray(
        $directory,
        $recursive = false,
        $listDirs = false,
        $listFiles = true,
        $exclude = '',
        $allowedFileTypes = [],
        $relativePath = false
    ) {
        $arrayItems = [];
        $skipByExclude = false;
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                preg_match(
                    '/(^(([\.]) {1,2})$|(\.(svn|git|md|htaccess))|(Thumbs\.db|\.DS_STORE|\.|\.\.))$/iu',
                    $file,
                    $skip
                );
                if ($exclude) {
                    preg_match($exclude, $file, $skipByExclude);
                }
                if ($allowedFileTypes && !is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                    $ext = strtolower(preg_replace('/^.*\.([^.]+)$/D', '$1', $file));
                    if (!in_array($ext, $allowedFileTypes)) {
                        $skip = true;
                    }
                }
                if (!$skip && !$skipByExclude) {
                    if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                        if ($recursive) {
                            $arrayItems = array_merge(
                                $arrayItems,
                                self::directoryToArray(
                                    $directory . DIRECTORY_SEPARATOR . $file,
                                    $recursive,
                                    $listDirs,
                                    $listFiles,
                                    $exclude,
                                    $allowedFileTypes,
                                    $relativePath
                                )
                            );
                        }
                        if ($listDirs) {
                            $arrayItems[] = (
                            $relativePath
                                ? $file
                                : $directory . DIRECTORY_SEPARATOR . $file
                            );
                        }
                    } else {
                        if ($listFiles) {
                            $arrayItems[] = (
                            $relativePath
                                ? $file
                                : $directory . DIRECTORY_SEPARATOR . $file
                            );
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $arrayItems;
    }

    /**
     * Delete all contents from a directory
     * @param string $directory Directory path
     * @param boolean $DELETE_DIRECTORY *[Optional]*, if is set directory will be deleted too.
     * @return bool
     */
    public static function deleteDirectoryContents($directory, $DELETE_DIRECTORY = false)
    {
        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            $path = self::getPath([$directory, $file]);
            (
                is_dir($path)
                ? self::deleteDirectoryContents($path, true)
                : unlink($path)
            );
        }

        return $DELETE_DIRECTORY ? rmdir($directory) : true;
    }
}
