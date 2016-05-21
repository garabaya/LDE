<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:19
 */

namespace lde;

use Carbon\Carbon;
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
        'id','title', 'description', 'community_id', 'community_rule_id', 'thread_id', 'supported', 'approved'
    ];

    public function creator()
    {
        return $this->belongsTo('lde\Community');
    }

    public function rule()
    {
        return $this->belongsTo('lde\CommunityRule','community_rule_id');
    }

    public function thread()
    {
        return $this->hasOne('lde\Thread','id','thread_id');
    }

    public function supportedBy()
    {
        return $this->belongsToMany('lde\Community', 'metasupports', 'metaInitiative_id');
    }

    public function votedBy()
    {
        return $this->belongsToMany('lde\Community', 'metavotes');
    }

    /**
     * This function checks if the metainitiative is supported for the needed people to be voted.
     * It should be called every time the initiative gets a new support.
     * At the time that the initiative gets the needed supporters, it must pass to the voting period.
     *
     * @return mixed
     */
    public function checkSupport()
    {
        if ($this->supported==null){
            $community = $this->rule->community;
            $expireDays = intval($community->rules()->where('rule_id',2)->first()->pivot->value);
            $expireDate = $this->created_at->addDays($expireDays);
            $date = Carbon::now();
            if ($expireDate>$date){
                $percentNeeded = intval($community->rules()->where('rule_id',3)->first()->pivot->value);
                $users_count = $community->users()->count();
                //Community's users needed supporting the initiative
                $needed = intval(ceil($users_count / 100.0 * $percentNeeded));
                if ($this->supportedBy()->count()>=$needed){
                    //the voting period starts now and his duration is calculated with de updated_at column
                    //  updated now and the voting duration rule of the community
                    //TODO send notifications to community users about a new initiative to be voted
                    $this->supported=true;
                    $this->save();
                    return true;
                }else return false;
            }else{
                $this->supported=false;
                $this->save();
                return false;
            }
        }else return $this->supported;
    }
}