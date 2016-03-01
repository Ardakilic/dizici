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
 * Class Serie.
 */
class Serie extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'external_id', 'title', 'image', 'premiered',
    ];

    /**
     * @return mixed
     */
    public function episodes()
    {
        return $this->hasMany('\App\Models\Episode', 'serie_id_internal');
    }
}
