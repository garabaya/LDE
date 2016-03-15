<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:29
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Join
 * @package lde
 *
 * An user joins in a community
 */
class Join extends Model
{
    protected $table = 'joins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * user_id: The user that is enrolled in a community
     * community_id: The community in which a user is enrolled
     */
    protected $fillable = [
        'id','user_id', 'community_id'
    ];
}