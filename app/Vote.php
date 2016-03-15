<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 15/03/2016
 * Time: 10:08
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Vote
 * @package lde
 *
 * A community vote an initiative
 */
class Vote extends Model
{
    protected $table = 'votes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that votes an initiative
     * initiative-id: The initiative voted by a community
     * value: The vote itself
     */
    protected $fillable = [
        'id','community_id', 'initiative_id', 'value'
    ];
}