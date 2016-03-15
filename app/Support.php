<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:34
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Support
 * @package lde
 *
 * A community supports an initiative
 */
class Support extends Model
{
    protected $table = 'supports';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that supports an initiative
     * initiative-id: The initiative supported by a community
     */
    protected $fillable = [
        'id','community_id', 'initiative_id'
    ];
}