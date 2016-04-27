<?php
/**
 * Author: Rubén Garabaya Arenas
 * Date: 15/03/2016
 * Time: 10:05
 */

namespace lde;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MetaSupport
 * @package lde
 *
 * A community supports a meta-initiative
 */
class MetaSupport extends Model
{
    protected $table = 'metasupports';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     * community_id: The community that supports an meta-initiative
     * metaInitiative_id: The meta-initiative supported by a community
     */
    protected $fillable = [
        'id','community_id', 'metaInitiative_id'
    ];

}