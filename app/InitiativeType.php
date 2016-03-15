<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:28
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InitiativeType
 * @package lde
 *
 * Initiatives types (at first only 'general' type)
 * This way you can delegate according to the type of iniciative
 */
class InitiativeType extends Model
{
    protected $table = 'initiativeTypes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','type'
    ];

    public function initiatives()
    {
        return $this->hasMany('lde\Initiative');
    }
}