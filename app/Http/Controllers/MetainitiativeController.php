<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\CommunityRule;
use lde\Http\Requests;
use lde\MetaInitiative;
use lde\MetaSupport;
use lde\Rule;
use lde\User;

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
            $comments = $metainitiative->thread->comments;
            foreach ($comments as $comment) {
                $comment->username = User::find($comment->user_id)->name;
            }
            $users = $community->users()->get();
            $supporters = MetaSupport::where([
                ['metainitiative_id',$metainitiative->id]
            ]);
            $supporters_count = $supporters->count();
            $metainitiative->supporters=$supporters_count;

            $supporting = false;
            $me = Auth::user();
            $wrapper = $me->wrapper($community->id);
            foreach($supporters->get() as $supporter){
                if ($supporter->community_id==$wrapper->id){
                    $supporting=true;
                    break;
                }
            }



            if ($users->contains($me)) {
                return view('metainitiative.show', [
                    'metainitiative' => $metainitiative,
                    'rule' => $rule,
                    'comments' => $comments,
                    'supporting' => $supporting
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
}
