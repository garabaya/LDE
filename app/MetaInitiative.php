<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:19
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MetaInitiative
 * @package lde
 *
 * A type of initiative that pretends to change a community's rule
 */
class MetaInitiative extends Model
{
    protected $table = 'metaInitiatives';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * title: Initiative's title
     * description: Initiative's description
     * community_id: The community that proposes an metaInitiative
     * rule_id: The rule that a community wants to change with the metaInitiative
     * thread_id: The thread where users discuss about the metaInitiative
     */
    protected $fillable = [
        'id','title', 'description', 'community_id', 'rule_id', 'thread_id'
    ];

    public function creator()
    {
        return $this->belongsTo('lde\Community');
    }

    public function rule()
    {
        return $this->belongsTo('lde\Rule');
    }

    public function thread()
    {
        return $this->hasOne('lde\Thread');
    }

    public function supportedBy()
    {
        return $this->belongsToMany('lde\Community', 'metaSupport');
    }

    public function votedBy()
    {
        return $this->belongsToMany('lde\Community', 'metaVote');
    }
}