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
class WatchlistGroup extends Model
{
    protected $table = 'watchlist_groups';
    public $timestamps = false;

    protected $fillable = [
        'title',
    ];

    public function watchlists(){
        return $this->hasMany('\App\Models\Watchlist', 'watchlist_group_id');
    }

}