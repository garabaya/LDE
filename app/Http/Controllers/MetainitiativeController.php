<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\CommunityRule;
use lde\Http\Requests;
use lde\MetaInitiative;
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
            $community = Community::find($metainitiative->rule->community_id);
            $percentneeded = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '3']
            ])->first()->value);
            $users_count = $community->users()->count();
            $needed = intval(ceil($users_count / 100.0 * $percentneeded));
            $metainitiative->needed = $needed;
            $rule = Rule::find($metainitiative->rule->rule_id);
            $expireDays = intval(CommunityRule::where([
                ['community_id', $community->id],
                ['rule_id', '2']
            ])->first()->value);
            $metainitiative->expireDate = $metainitiative->created_at->addDays($expireDays);
            $rule->value = $metainitiative->rule->value;
            $rule->newValue = $metainitiative->value;
            $user = User::find(Community::find($metainitiative->community_id)->user_id);
            $rule->user = $user->name;
            $comments = $metainitiative->thread->comments;
            foreach ($comments as $comment) {
                $comment->username = User::find($comment->user_id)->name;
            }
            $users = $community->users()->get();
            if ($users->contains(Auth::user())) {
                return view('metainitiative.show', [
                    'metainitiative' => $metainitiative,
                    'rule' => $rule,
                    'comments' => $comments
                ]);
            } else {
                return Redirect::to('/')->withErrors(array(
                    'danger' => ['You are not allowed']));
            }
        }

    }
}
