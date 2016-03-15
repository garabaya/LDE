<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 13:47
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Thread
 * @package lde
 *
 * Discussion thread (like a forum)
 */
class Thread extends Model
{
    protected $table = 'threads';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * title: Thread's title
     * description: Thread's description
     * community_id: Community scoped by the thread
     * type: Thread's type (at first 'general' and 'initiative discussion thread')
     */
    protected $fillable = [
        'id','title', 'description', 'community_id', 'type'
    ];

    public function initiative(){
        return $this->belongsTo('lde\Initiative');
    }

    public function comments()
    {
        return $this->hasMany('lde\Comment');
    }

    public function scope()
    {
        return $this->belongsTo('lde\Community');
    }
}