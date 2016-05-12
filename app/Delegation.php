<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 14/03/2016
 * Time: 16:32
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Delegation
 * @package lde
 *
 * A community delegates vote or support in another
 */
class Delegation extends Model
{
    protected $table = 'delegations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that delegates vote/support
     * delegated_id: The community that is delegated in
     * initiativeType_id: The type of the initiative wich is delegating
     * This 3 attributes must implement an unique-key because you only can delegate your vote
     *          in just one community for each type of initiative
     */
    protected $fillable = [
        'id','community_id', 'delegated_id', 'initiativeType_id'
    ];

    /**
     * Returns the vote weight of an user in a specific community (throws his wrapper) in an specific
     * initiative type
     * (i.e delegated votes count for the specific initiative type)
     *
     * @param $wrapper_id
     * @param $initiativeType_id
     * @return int|null
     */
    public static function user_weight($wrapper_id, $initiativeType_id)
    {
        $votesCount=1;//own vote
        $wrapper = Community::find($wrapper_id);
        if ($wrapper!=null){
            $delegations = Delegation::where([
                'delegated_id'=>$wrapper_id,
                'initiativeType_id'=>$initiativeType_id
            ])->get();
            foreach($delegations as $delegation){
                $votesCount+=Delegation::user_weight($delegation->community_id,$initiativeType_id);
            }
            return $votesCount;
        }else return null;
    }
}