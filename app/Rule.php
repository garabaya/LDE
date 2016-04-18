<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 12:55
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Rule
 * @package lde
 *
 * Each of the rules governing the functioning of a community
 */
class Rule extends Model
{
    protected $table = 'rules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * value: Usually a boolean or numeric value for the rule
     * type: boolean, numeric, list,...
     * description: Rule's description
     * community_id: The community affected
     */
    protected $fillable = [
        'id', 'value', 'type', 'description'
    ];

    /**
     * @return mixed Return the communities that use the rule and the 'value' column of the pivot
     * table for each community
     *
     * I'm not sure this will be useful for something
     */
    public function communities()
    {
        return $this->belongsToMany('lde\Community', 'community_rule')->withPivot('value');
    }
}