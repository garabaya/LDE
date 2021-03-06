<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 13/03/2016
 * Time: 17:39
 */
namespace lde;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Community
 * @package lde
 *
 * A group of users
 * Every user belongs to a single group formed by himself. This way the delegation is always between communities.
 */
class Community extends Model
{

    /**
     * @var array
     *
     * Default rules for a new Super Community
     */
    public static $rules = [
        '1' => 'true', /** public */
        '2' => '15', /** available days to support an initiative */
        '3' => '10', /** percentage of supporters required for an initiative to be voted */
        '4' => '30'/** available days to vote an initiative */
    ];
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
        return $this->hasOne('lde\User', 'id', 'user_id');
    }

    public function super()
    {
        return $this->belongsTo('lde\Community');
    }

    public function subCommunities()
    {
        return $this->hasMany('lde\Community');
    }

    public function delegateIn($initiativeType_id = null)
    {
        if ($initiativeType_id == null) return $this->belongsToMany('lde\Community', 'delegations', 'community_id', 'delegated_id')->withPivot('id', 'initiativeType_id');
        else return $this->belongsToMany('lde\Community', 'delegations', 'community_id', 'delegated_id')->withPivot('id', 'initiativeType_id')->wherePivot('initiativeType_id', $initiativeType_id);
    }

    public function delegatedBy()
    {
        return $this->belongsToMany('lde\Community', 'delegations', 'delegated_id', 'community_id')->withPivot('id', 'initiativeType_id');
    }

    public function users()
    {
        return $this->belongsToMany('lde\User', 'joins');
    }

    public function propose($community_id)
    {
        if ($community_id == null) return $this->hasMany('lde\Initiative');
        else return $this->hasMany('lde\Initiative')->where('scoped_id', $community_id);
    }

    public function scopedBy()
    {
        return $this->hasMany('lde\Initiative', 'scoped_id');
    }

    /**
     * @return mixed Return the rules of a community including the 'value' column of the pivot table
     */
    public function rules()
    {
        return $this->belongsToMany('lde\Rule', 'community_rule')->withPivot('value');
    }

    public function metapropose($community_id = null)
    {
        if ($community_id == null) return $this->hasMany('lde\MetaInitiative');
        else return $this->hasMany('lde\MetaInitiative')->whereIn('community_rule_id', CommunityRule::select('id')->where('community_id', $community_id)->get());

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

    /**
     * @param $query
     * @param $paginate elements in each page
     * @return communities sorted by popularity with pagination
     */
    public function scopePopular($query)
    {
        $comIds = Join::select('community_id')
            ->from('joins')->groupBy('community_id')->orderBy(DB::raw('count(*)'), 'DESC')->get();
        $comIdsStr = '';
        foreach ($comIds as $comId) {
            $comIdsStr .= ',' . $comId->community_id;
        }
        return $query->where('type', 'general')->whereIn('id', $comIds)->orderByRaw(DB::raw('FIELD(id,0' . $comIdsStr . ')'));
    }

    public function isJoined()
    {
        return $this->users()->get()->contains(Auth::user());
    }

    public function metaInitiatives()
    {
        $community_rule_ids = CommunityRule::select('id')->where('community_id', $this->id)->get();
        $metaInitiatives = MetaInitiative::whereIn('community_rule_id', $community_rule_ids)->orderBy('created_at')->get();
        return $metaInitiatives;
    }

    public function isInDelegatingLine($community_id, $initiativeType_id)
    {
        //Is he delegating in somebody?
        $delegation = Community::find($community_id)->delegation($initiativeType_id);
        if ($delegation == null) return false;// He is not delegating
        elseif ($delegation->delegated_id == $this->id) return true;//He is delegating in me
        else return $this->isInDelegatingLine($delegation->delegated_id, $initiativeType_id);//He is delegating in somebody but not in me

    }

    public function delegation($initiativeType_id)
    {
        return $delegation = Delegation::where([
            'community_id' => $this->id,
            'initiativeType_id' => $initiativeType_id
        ])->first();
    }

    /**
     * it makes or undoes a delegation
     * @param $delegated_id
     * @param $initiativeType_id
     * @return bool
     */
    public function delegate($delegated_id, $initiativeType_id)
    {
        try {
            $delegated = Community::findOrFail($delegated_id);
            if ($this->community_id != $delegated->community_id) throw new Exception('Delegating and delegated communities must have the same super-community');
            $delegation = Delegation::where([
                'community_id' => $this->id,
                'delegated_id' => $delegated_id,
                'initiativeType_id' => $initiativeType_id
            ])->first();
            if ($delegation == null) {
                $oldDelegation = Delegation::where([
                    'community_id' => $this->id,
                    'initiativeType_id' => $initiativeType_id
                ])->first();
                DB::beginTransaction();
                if ($oldDelegation != null) {//If already exists a delegation in other user,
                    //we make a recursive call to undo this delegation(detach)
                    $this->delegate($oldDelegation->delegated_id, $initiativeType_id);
                }
                $this->delegateIn()->attach($delegated_id, array('initiativeType_id' => $initiativeType_id));//attach the new delegation
            } else {
                Delegation::where([
                    'community_id' => $this->id,
                    'delegated_id' => $delegated_id,
                    'initiativeType_id' => $initiativeType_id
                ])->delete();
//                $this->delegateIn()->detach($delegated_id,array('initiativeType_id'=>$initiativeType_id));//detach the delegation
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }

    }

    public function popularity()
    {
        return Delegation::where([
            'delegated_id' => $this->id
        ])->count();
    }

    public function support($initiative)
    {
        if (get_class($initiative) == 'lde\MetaInitiative') {
            $community = $initiative->rule->community;
            $expireDays = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '2']
            ])->first()->value);
            $expireDate = $initiative->created_at->addDays($expireDays);
            $date = Carbon::now();
            //If the user is joined the metainitiative's community and
            // is not supporting it yet and
            // is not expired
            // then can support it
            if ($this->creator->communities->contains($community) &&
                !$initiative->supportedBy->contains($this) &&
                $expireDate > $date
            ) {

                $metasupport = new MetaSupport();
                $metasupport->community_id = $this->id;
                $metasupport->metaInitiative_id = $initiative->id;
                $response = $metasupport->save();
                $initiative->checkSupport();
                return $response;
            } else {
                $initiative->checkSupport();
                return false;
            }
        } else {
            $community = Community::find($initiative->scoped_id);
            $expireDays = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '2']
            ])->first()->value);
            $expireDate = $initiative->created_at->addDays($expireDays);
            $date = Carbon::now();
            //If the user is joined the metainitiative's community and
            // is not supporting it yet and
            // is not expired
            // then can support it
            if ($this->creator->communities->contains($community) &&
                !$initiative->supportedBy->contains($this) &&
                $expireDate > $date
            ) {

                $support = new Support();
                $support->community_id = $this->id;
                $support->initiative_id = $initiative->id;
                $response = $support->save();
                $initiative->checkSupport();
                return $response;
            } else {
                $initiative->checkSupport();
                return false;
            }
        }
    }

    public function vote($initiative, $vote)
    {

    }

    public function getVote($initiative)
    {
        if (get_class($initiative) == 'lde\MetaInitiative') {
            if ($initiative->votedBy->contains($this)) {
                $metavote = MetaVote::where([
                    'community_id' => $this->id,
                    'metaInitiative_id' => $initiative->id
                ])->first();
                return $metavote->value;
            } else {
                return null;
            }
        } else {
            if ($initiative->votedBy->contains($this)) {
                $vote = Vote::where([
                    'community_id' => $this->id,
                    'initiative_id' => $initiative->id
                ])->first();
                return $vote->value;
            } else {
                $delegation = $this->delegation($initiative->initiativeType_id);
                if ($delegation == null) {
                    return null;
                } else {
                    return Community::find($delegation->delegated_id)->getVote($initiative);
                }
            }
        }
    }
}