<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:23
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 * @package lde
 *
 * Each of comments belonging to a thread
 */
class Comment extends Model
{
    protected $table = 'comments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * user_id: The user that comments in a thread
     * thread_id: The thread that owns a user's comment
     */
    protected $fillable = [
        'id','text', 'user_id', 'thread_id'
    ];

    public function owner()
    {
        return $this->belongsTo('lde\User');
    }

    public function thread()
    {
        return $this->belongsTo('lde\Thread');
    }
}