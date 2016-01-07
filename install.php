<?php

/**
 * Dizici
 * https://github.com/Ardakilic/dizici
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link        https://github.com/Ardakilic/dizici
 * @copyright   2016 Arda Kilicdagi. (https://arda.pw/)
 * @license     http://opensource.org/licenses/MIT - MIT License
 */

//The folder that dizici will be installed to:
$folder = defined('CONFIG_ROOT') ? CONFIG_ROOT : (getenv('HOME') . '/.dizici/');

//First, delete the .dizici folder from user's home if exists
if (is_dir($folder)) {
    delTree($folder);
}

//Then create the folder
mkdir($folder, 0775);

//Copy the configuration file
copy(__DIR__ . '/config.sample.yml', $folder . 'config.yml');

//Create a blank .sqlite Database
touch($folder . 'dizici.sqlite');


//Taken from PHP.net/rmdir
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}