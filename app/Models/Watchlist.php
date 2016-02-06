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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WatchlistGroup
 * @package App\Models
 */
class Watchlist extends Model
{
    protected $table = 'watchlists';
    public $timestamps = false;

    protected $fillable = [
        'watchlist_group_id',
        'tvmaze_id',
    ];

    public function group() {
        return $this->belongsTo('\App\Models\WatchlistGroup', 'watchlist_group_id');
    }

    public function show(){
        return $this->hasOne('\App\Models\Serie', 'external_id', 'tvmaze_id');
    }

}