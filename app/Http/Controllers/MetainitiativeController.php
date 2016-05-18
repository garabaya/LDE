<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\CommunityRule;
use lde\Http\Requests;
use lde\MetaInitiative;
use lde\MetaSupport;
use lde\Rule;
use lde\Thread;
use lde\User;
use Symfony\Component\HttpFoundation\Response;

class MetainitiativeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the specified resource.
     *
     * Only if you are joined in the community scoped by the metainitiative you will can see
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $metainitiative = MetaInitiative::find($id);
        if ($metainitiative == null) {
            abort(404);
        } else {
            //The community affected by this metainitiative
            $community = Community::find($metainitiative->rule->community_id);
            //Percent of supports needed to throw the initiative to the voting proccess
            $percentneeded = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '3']
            ])->first()->value);
            //Community's users count
            $users_count = $community->users()->count();
            //Community's users needed supporting the initiative
            $needed = intval(ceil($users_count / 100.0 * $percentneeded));
            $metainitiative->needed = $needed;
            //The rule to be changed for this initiative
            $rule = Rule::find($metainitiative->rule->rule_id);
            //Expiration days of the initiatives settted up in the community of this initiative
            $expireDays = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '2']
            ])->first()->value);
            $metainitiative->expireDate = $metainitiative->created_at->addDays($expireDays);
            //The value that is wanted to be changed with this initiative and the new value
            $rule->value = $metainitiative->rule->value;
            $rule->newValue = $metainitiative->value;
            //The user that proposed this initiative
            $user = User::find(Community::find($metainitiative->community_id)->user_id);
            $rule->user = $user->name;

            $users = $community->users()->get();
            $supporters = MetaSupport::where([
                ['metainitiative_id', $metainitiative->id]
            ]);
            $supporters_count = $supporters->count();
            $metainitiative->supporters = $supporters_count;

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
                return view('metainitiative.show', [
                    'metainitiative' => $metainitiative,
                    'rule' => $rule,
                    'thread' => $metainitiative->thread,
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
        $metaInitiative = MetaInitiative::find($request->id);
        if (Auth::user()->support($metaInitiative)) {
            return Redirect::back()->withErrors(array(
                'success' => ['You are supporting now this initiative']));
        } else {
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }

    public function create(Request $request)
    {
        $community = Community::find($request->community_id);
        if ($community == null || !$community->isJoined()) {
            abort(404);
        }
        $rules = array();
        foreach($community->rules()->get() as $rule){
            $pivot_id=CommunityRule::select('id')->where(array(
                'community_id' => $community->id,
                'rule_id' => $rule->id
            ))->first()->id;
            $rules[$pivot_id]=$rule->description;
        }
        return view('metainitiative.create', array(
            'community' => $community,
            'rules' => $rules
        ));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'value' => 'required'
        ]);
        $communityRule = CommunityRule::find($request->community_rule_id);
        if ($communityRule==null) abort(404);
        if ($communityRule->rule->type=='boolean'){
            if ($request->value!='true' || $request->value!='false'){
                return Redirect::back()->withErrors(array(
                    'danger' => ['Something went wrong'],
                    'value' => ['Not the expected value']))->withInput();            }
        }else{
            if(!is_numeric($request->value)){
                return Redirect::back()->withErrors(array(
                    'danger' => ['Something went wrong'],
                    'value' => ['Must be numeric']))->withInput();
            }
        }

        // assume it won't work
        $success = false;

        DB::beginTransaction();

        try {
            //Each initiative have is own discussion thread
            $thread=new Thread();

            $thread->community_id=$communityRule->community_id;
            $thread->title=$request->title;
            $thread->description=$request->description;
            $thread->type='initiative discussion thread';

            if ($thread->save()){
                $metainitiative = new MetaInitiative();
                $user = Auth::user();

                $metainitiative->community_id=$user->wrapper($communityRule->community_id)->id;
                $metainitiative->community_rule_id=$request->community_rule_id;
                $metainitiative->title=$request->title;
                $metainitiative->description=$request->description;
                $metainitiative->value=$request->value;
                $metainitiative->thread_id=$thread->id;
                if ($metainitiative->save()) $success=true;
            }
        } catch (\Exception $e) {

        }
        if ($success) {
            DB::commit();
            return Redirect::to('/metainitiative/'.$metainitiative->id)->withErrors(array(
                'success' => ['Rule initiative created successfully.']));
        } else {
            DB::rollback();
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }

    /**
     * @param Request $request
     *
     * The form to create a new metainitiative gets the type of the rule that the user has selected
     * throw an ajax request and change the fields dinamically
     * This function responds to that ajax request with the type and actual value of the rule selected
     * @return array
     */
    public function ruleSelected(Request $request)
    {
            $selected = CommunityRule::find($request->id);
            $rule=Rule::find($selected->rule_id);
            $response = array('value'=>$selected->value,'type'=>$rule->type);
            return $response;
    }
}
