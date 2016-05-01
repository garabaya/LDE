<?php

namespace lde;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function communities()
    {
        return $this->belongsToMany('lde\Community', 'joins');
    }

    public function createdCommunities()
    {
        return $this->hasMany('lde\Community');
    }

    public function comments()
    {
        return $this->hasMany('lde\Comment');
    }

    /**
     * @param $community_id
     * @return Community
     */
    public function wrapper($community_id)
    {
        $wrapper = Community::where([
            ['type', 'single'],
            ['community_id',$community_id],
            ['user_id',$this->id]
        ]);
        return $wrapper->first();
    }
    public function support($initiative)
    {
        if (get_class($initiative)=='lde\MetaInitiative'){
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
            if ($this->communities->contains($community) &&
                !$initiative->supportedBy->contains($this->wrapper($initiative->community_id)) &&
                $expireDate>$date){

                $metassuport = new MetaSupport();
                $metassuport->community_id=$this->wrapper($initiative->rule->community->id)->id;
                $metassuport->metaInitiative_id=$initiative->id;
                return $metassuport->save();
            }else return false;
        }else{
            //TODO support iniciativa de la clase Initiative
            return false;
        }
    }
}
