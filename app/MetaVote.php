<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 15/03/2016
 * Time: 10:11
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MetaVote
 * @package lde
 *
 * A community vote an meta-initiative
 */
class MetaVote extends Model
{
    protected $table = 'metaVotes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that votes an meta-initiative
     * metaInitiative_id: The meta-initiative voted by a community
     * value: The vote itself
     */
    protected $fillable = [
        'id','community_id', 'metaInitiative_id', 'value'
    ];
}