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

require_once __DIR__ . '/vendor/autoload.php';

define('APP_VERSION', '1.0.1');

use Symfony\Component\Yaml\Yaml;

define('CONFIG_ROOT', getenv('HOME') . '/.dizici/');

//If no configuration found, we'll assume the app is not installed, and run the installer script
if (!file_exists(CONFIG_ROOT . 'config.yml')) {
    require_once __DIR__ . '/install.php';
}

$config = Yaml::parse(file_get_contents(CONFIG_ROOT . 'config.yml'));

date_default_timezone_set($config['timezone']);

//Database handler
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

//If connection driver is SQLite, we'll have to prepend the config root path to the database:
if ($config['connection']['driver'] == 'sqlite') {
    $config['connection']['database'] = CONFIG_ROOT . $config['connection']['database'];
}

//Connect to the database
$capsule->addConnection($config['connection']);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();