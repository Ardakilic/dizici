<?php

/**
 * Dizici
 * https://github.com/Ardakilic/dizici.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link        https://github.com/Ardakilic/dizici
 *
 * @copyright   2016 Arda Kilicdagi. (https://arda.pw/)
 * @license     http://opensource.org/licenses/MIT - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Episode.
 */
class Episode extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'external_id',
        'serie_id_internal', 'serie_id_external',
        'season_id', 'episode_id', 'is_special',
        'title', 'description', 'url', 'image',
        'airdate',
    ];

    /**
     * @return mixed
     */
    public function serie()
    {
        return $this->belongsTo('\App\Models\Serie', 'serie_id_internal');
    }
}
