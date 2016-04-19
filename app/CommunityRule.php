<?php

namespace lde;

use Illuminate\Database\Eloquent\Model;

class CommunityRule extends Model
{
    protected $table = 'community_rule';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * value: Usually a boolean or numeric value for the rule
     * rule_id: The rule that affects the community
     * community_id: The community affected
     */
    protected $fillable = [
        'id', 'community_id', 'rule_id', 'value'
    ];

    public function initiatives()
    {
        return $this->hasMany('lde\MetaInitiative','community_rule_id');
    }
}
