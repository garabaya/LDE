<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 13:02
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Initiative
 * @package lde
 *
 * A type of initiative that aims to be voted on by the community
 */
class Initiative extends Model
{
    protected $table = 'initiatives';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * title: Initiative's title
     * description: Initiative's description
     * community_id: The community that proposes an initiative
     * scoped_id: Community scoped by the initiative
     * initiativeType_id: Initiative's type
     * thread_id: The thread where users discuss about the initiative
     */
    protected $fillable = [
        'id','title', 'description', 'community_id', 'scoped_id', 'initiativeType_id', 'thread_id'
    ];

    public function thread()
    {
        return $this->hasOne('lde\Thread','id');
    }

    public function type(){
        return $this->belongsTo('lde\InitiativeType','initiativeType_id');
    }

    public function creator()
    {
        return $this->belongsTo('lde\Community');
    }

    public function scope()
    {
        return $this->belongsTo('lde\Community', 'scoped_id');
    }

    public function supportedBy()
    {
        return $this->belongsToMany('lde\Community', 'supports');
    }

    public function votedBy()
    {
        return $this->belongsToMany('lde\Community', 'vote');
    }
}