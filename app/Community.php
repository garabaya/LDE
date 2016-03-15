<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 13/03/2016
 * Time: 17:39
 */
namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Community
 * @package lde
 *
 * A group of users
 * Every user belongs to a single group formed by himself. This way the delegation is always between communities.
 */
class Community extends Model
{
    protected $table = 'communities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * name: Community's name
     * type: Three types: single, lobby and super
     *          single is an user wrapper that allows him delegate his vote in another community
     *          lobby is a sub-community with its own rules whose users vote together to have more power inside the
     *              super-community
     *          super is the community that wraps all other
     * description: Community's description
     * user_id: Community`s creator
     * community_id: The upper community
     */
    protected $fillable = [
        'id', 'name', 'type', 'description', 'user_id', 'community_id'
    ];

    public function creator()
    {
        return $this->belongsTo('lde\User');
    }

    public function super()
    {
        return $this->belongsTo('lde\Community');
    }

    public function subCommunities()
    {
        return $this->hasMany('lde\Community');
    }

    public function delegateIn()
    {
        return $this->belongsToMany('lde\Community', 'delegation', 'community_id', 'delegated_id');
    }

    public function delegatedBy()
    {
        return $this->belongsToMany('lde\Community', 'delegation', 'delegated_id', 'community_id');
    }

    public function users()
    {
        return $this->belongsToMany('lde\User', 'join');
    }

    public function propose()
    {
        return $this->hasMany('lde\Initiative');
    }

    public function scopedBy()
    {
        return $this->hasMany('lde\Initiative', 'scoped_id');
    }

    public function rules()
    {
        return $this->hasMany('lde\Rule');
    }

    public function metapropose()
    {
        return $this->hasMany('lde\MetaInitiative');
    }

    public function supportedInitiatives()
    {
        return $this->belongsToMany('lde\Initiative', 'support');
    }

    public function supportedMetaInitiatives()
    {
        return $this->belongsToMany('lde\MetaInitiative', 'metaSupport');
    }

    public function votedInitiatives()
    {
        return $this->belongsToMany('lde\Initiative', 'vote');
    }

    public function votedMetaInitiatives()
    {
        return $this->belongsToMany('lde\MetaInitiative', 'metaVote');
    }
}