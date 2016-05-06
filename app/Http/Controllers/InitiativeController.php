<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\CommunityRule;
use lde\Http\Requests;
use lde\Initiative;
use lde\InitiativeType;
use lde\Support;
use lde\Thread;
use lde\User;

class InitiativeController extends Controller
{
    public function create(Request $request)
    {
        $community = Community::find($request->community_id);
        if ($community == null || !$community->isJoined()) {
            abort(404);
        }
        $types=array();
        foreach(InitiativeType::get() as $initiativetype){
            $types[$initiativetype->id]=$initiativetype->type;
        }
        return view('initiative.create', array(
            'community' => $community,
            'types' => $types
        ));
    }
    public function store(Request $request)
    {

        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'initiativeType_id' => 'required|numeric'
        ]);

        $community = Community::find($request->community_id);
        if ($community==null)abort(404);
        if (!$community->users()->get()->contains(Auth::user())) abort(404);

        // assume it won't work
        $success = false;

        DB::beginTransaction();

        try {
            //Each initiative have is own discussion thread
            $thread=new Thread();

            $thread->community_id=$community->id;
            $thread->title=$request->title;
            $thread->description=$request->description;
            $thread->type='initiative discussion thread';

            if ($thread->save()){
                $initiative = new Initiative();
                $user = Auth::user();

                $initiative->community_id=$user->wrapper($community->id)->id;
                $initiative->scoped_id=$community->id;
                $initiative->title=$request->title;
                $initiative->description=$request->description;
                $initiative->initiativeType_id=$request->initiativeType_id;
                $initiative->thread_id=$thread->id;
                if ($initiative->save()) $success=true;
            }
        } catch (\Exception $e) {

        }
        if ($success) {
            DB::commit();
            return Redirect::to('/initiative/'.$initiative->id)->withErrors(array(
                'success' => ['Initiative created successfully.']));
        } else {
            DB::rollback();
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }

    public function show($id)
    {
        $initiative = Initiative::find($id);
        if ($initiative == null) {
            abort(404);
        } else {
            //The community affected by this metainitiative
            $community = Community::find($initiative->scoped_id);
            //Percent of supports needed to throw the initiative to the voting proccess
            $percentneeded = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '3']
            ])->first()->value);
            //Community's users count
            $users_count = $community->users()->count();
            //Community's users needed supporting the initiative
            $needed = intval(ceil($users_count / 100.0 * $percentneeded));
            $initiative->needed = $needed;

            //Expiration days of the initiatives settted up in the community of this initiative
            $expireDays = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '2']
            ])->first()->value);
            $initiative->expireDate = $initiative->created_at->addDays($expireDays);

            //The user that proposed this initiative
            $user = User::find(Community::find($initiative->community_id)->user_id);
            $initiative->user=$user;


            $users = $community->users()->get();
            $supporters = Support::where([
                ['initiative_id', $initiative->id]
            ]);
            $supporters_count = $supporters->count();
            $initiative->supporters = $supporters_count;

            $supporting = false;
            $me = Auth::user();
            $wrapper = $me->wrapper($community->id);
            foreach ($supporters->get() as $supporter) {
                if ($supporter->community_id == $wrapper->id) {
                    $supporting = true;
                    break;
                }
            }


            if ($users->contains($me)) {
                return view('initiative.show', [
                    'initiative' => $initiative,
                    'thread' => $initiative->thread,
                    'supporting' => $supporting,
                    'community_id' => $community->id
                ]);
            } else {
                return Redirect::to('/')->withErrors(array(
                    'danger' => ['You are not allowed']));
            }
        }
    }

    public function support(Request $request)
    {
        $initiative = Initiative::find($request->id);
        if (Auth::user()->support($initiative)) {
            return Redirect::back()->withErrors(array(
                'success' => ['You are supporting now this initiative']));
        } else {
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }
}
