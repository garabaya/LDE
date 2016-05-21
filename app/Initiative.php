<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 13:02
 */

namespace lde;

use Carbon\Carbon;
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
        'id','title', 'description', 'community_id', 'scoped_id', 'initiativeType_id', 'thread_id', 'supported', 'approved'
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

    /**
     * This function checks if the initiative is supported for the needed people to be voted.
     * It should be called every time the initiative gets a new support.
     * At the time that the initiative gets the needed supporters, it must pass to the voting period.
     *
     * @return mixed
     */
    public function checkSupport()
    {
        if ($this->supported==null){
            $community = $this->scope()->first();
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