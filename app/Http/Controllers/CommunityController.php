<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\Delegation;
use lde\Http\Requests;
use lde\InitiativeType;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'Communities';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('community.create');
    }

    /**
     * Join or disjoin the user in a community
     * Request object contains the 'id' of the community which you want to join (or disjoin) in
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        try {
            $this->joinIn($request->id);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }

        return Redirect::back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'description' => 'required|max:255',
        ]);

        // assume it won't work
        $success = false;

        DB::beginTransaction();

        try {
            //Creating a new community
            $community = new Community;
            $user = Auth::user();

            $community->name=$request->name;
            $community->description=$request->description;
            $community->user_id=$user->id;
            $community->type='general';


            //joining the user in the new community
            if ($community->save()) {
                $this->joinIn($community->id);
                foreach(Community::$rules as $rule => $value){
                    $community->rules()->attach($rule, ['value' => $value]);
                }
                $success = true;
            }
        } catch (\Exception $e) {

        }

        if ($success) {
            DB::commit();
            return Redirect::to('/')->withErrors(array(
                'success' => ['Community '.$community->name.' created successfully.']));
        } else {
            DB::rollback();
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }

    /**
     * Display the specified resource.
     *
     * The community's view displayed is different if you are joined or not
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $community = Community::find($id);
        $users = $community->users()->get();
        if ($users->contains(Auth::user())){
            return view('community.joined', [
                'com' => $community
            ]);
        }else{
            return view('community.show', [
                'com' => $community
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * The registered user joins a community
     *
     * @param int $community_id id of the community that the user wants to join
     */
    public function joinIn($community_id)
    {
        $user = Auth::user();
        $community = Community::find($community_id);
        $community_type = $community->type;
        // Disjoin
        if ($user->communities()->get()->contains($community)){
            //We have to delete the wrapping community if the user is disjoining in a general community (not a lobby)
            if ($community_type=='general'){
                $wrap = Community::where([
                    ['user_id',$user->id],
                    ['community_id',$community->id]
                ])->first();
                $wrap->delete();
            }
            //delete the realtionship throw pivot (joins table)
            $user->communities()->detach($community_id);

            //Join
        }else{
            //We have to wrap the user in a single community if he is joining in a general community (not a lobby)
            if ($community_type=='general'){
                $wrap = new Community;
                $wrap->name=$user->id.'_wrapper';
                $wrap->description='Wrapper community';
                $wrap->user_id=$user->id;
                $wrap->type='single';
                $wrap->community_id=$community_id;
                $wrap->save();
            }
            //make new realtionship throw pivot (joins table)
            $user->communities()->attach($community_id);
        }

    }

    public function createInitiative($id)
    {
        return view('community.createInitiative',array(
            'id'=>$id
        ));
    }

    /**
     * @param Request $request
     *
     * This function responds to an Ajax request to undo a delegation
     * @return string The html panel with the rest of delegations (if exist)
     */
    public static function ajax_undo(Request $request)
    {
        $response='';
        $id=$request->id;
        $community_id=$request->community_id;
        $delegated_id=$request->delegated_id;
        $initiativeType_id=$request->initiativeType_id;
        //Have we received all parameters?
        if ($id!=null&&$community_id!=null&&$delegated_id!=null&&$initiativeType_id!=null){
            //The delegations only can be deleted by the user that is delegating his vote
            if (Auth::user()->id==Community::find($community_id)->user_id){
                $delegation=Delegation::find($id);
                //Validating data
                if ($delegation->community_id==$community_id && $delegation->delegated_id==$delegated_id && $delegation->initiativeType_id==$initiativeType_id){
                    $delegation->delete();
                }
            }
        }
        $delegations = Community::find($community_id)->delegateIn()->get();
        foreach($delegations as $com_delegated){
            $com_delegated->initiativeType=InitiativeType::find($com_delegated->pivot->initiativeType_id)->type;
        }
        if (count($delegations)>0){
            $response='<div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2>My vote delegations</h2>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Initiative type</th>
                                    <th>Delegating in</th>
                                    <th></th>
                                </tr>
                                </thead>';
            foreach($delegations as $delegation){
                $response.='<tr>
                                        <td>'.$delegation->initiativeType.'</td>
                                        <td>
                                            <a href="'.action('UserController@show').'/'.$delegation->id.'">'.$delegation->creator->name.'</a>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    data-communityId="'.$community_id.'"
                                                    data-delegatedId="'.$delegation->id.'"
                                                    data-initiativeTypeId="'.$delegation->pivot->initiativeType_id.'"
                                                    data-pivot="'.$delegation->pivot->id.'"
                                                    class="btn btn-danger pull-right btn-join undo">Undo delegation
                                            </button>
                                        </td>
                                    </tr>';
            }
            $response.='</table>
                        </div>
                    </div>
                </div>';
        }



        return $response;

    }

    public function delegate(Request $request)
    {
        $this->validate($request, [
            'community_id' => 'required|integer',
            'delegated_id' => 'required|integer',
            'initiativeType_id' => 'required|integer'
        ]);
        $community_id = $request->community_id;
        $delegated_id = $request->delegated_id;
        $initiativeType_id=$request->initiativeType_id;

        try {
            if (!Auth::user()->wrapper($community_id)->delegate($delegated_id,$initiativeType_id)){
                return Redirect::back()->withErrors(array(
                    'danger' => ['Something went wrong']));
            }
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }

        return Redirect::back();
    }

    /**
     * @param $community_id
     * It shows the list of the community($community_id) members ordered by popularity
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function members($community_id)
    {
        $members = Community::where('community_id',$community_id)->get();
        foreach($members as $member){
            $member->popularity=$member->popularity();
        }
        $members=$members->sortByDesc('popularity');
        $com = Community::find($community_id);
        return view('community.members',[
            'members'=>$members,
            'com'=>$com,
            'initiativeTypes'=>InitiativeType::all()
        ]);
    }
}
