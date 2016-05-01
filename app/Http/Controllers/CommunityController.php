<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\Http\Requests;

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
}
