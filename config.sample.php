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

//Configuration file
return [

    //This is for here for better airdate handling
    'timezone' => 'Europe/Istanbul',

    //Database connection (Illuminate/Database addConnection array)
    //https://github.com/illuminate/database#usage-instructions
    'connection' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'root',
        'password' => 'password',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ],

    //Put the IDs of the Series here.
    //Example: http://www.tvmaze.com/shows/210/doctor-who , so it'll be 210
    //http://www.tvmaze.com/shows/659/torchwood, it'll be 659
    'series' => [
        210, 659, //Doctor who + Torchwood
        204, 206, 207, //Stargate SG-1, Stargate Atlantis, Stargate Universe
    ]

];