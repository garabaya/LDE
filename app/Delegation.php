<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:32
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Delegation
 * @package lde
 *
 * A community delegates vote or support in another
 */
class Delegation extends Model
{
    protected $table = 'delegations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that delegates vote/support
     * delegated_id: The community that is delegated in
     * initiativeType_id: The type of the initiative wich is delegating
     * This 3 attributes must implement an unique-key because you only can delegate your vote
     *          in just one community for each type of initiative
     */
    protected $fillable = [
        'id','community_id', 'delegated_id', 'initiativeType_id'
    ];
}