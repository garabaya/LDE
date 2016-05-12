<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use lde\Community;
use lde\Delegation;
use lde\Http\Requests;
use lde\InitiativeType;
use lde\User;

class UserController extends Controller
{
    public function show($wrapper_id=null)
    {
        //if there isn't $wrapper_id, the general user's settings will be shown
        if ($wrapper_id!=null){
            $wrapper = Community::find($wrapper_id);
            if ($wrapper==null) abort(404);
            if ($wrapper->type!='single') abort(404);
            $community = Community::find($wrapper->community_id);
            $me = Auth::user();
            if (!$community->users()->get()->contains($me)){
                return Redirect::back()->withErrors(array(
                    'danger' => ['You are not allowed.']));
            }
            $delegations = $wrapper->delegateIn();
            $weights = array();
            foreach(InitiativeType::get() as $initiativeType){
                $weights[$initiativeType->type]=Delegation::user_weight($wrapper_id,$initiativeType->id);
            }
            //TODO aqui
            //I want to see my own user information and settings as a community member
            if ($me->wrapper($community->id)==$wrapper){
                return view('community.me',[
                    'weights' => $weights,
                    'community' => $community,
                    'delegations' => $delegations
                ]);
            //I want to see the information community member
            }else{
                return view('community.user',[
                    'wrapper' => $wrapper,
                    'community' => $community,
                    'delegations' => $delegations
                ]);
            }
        //general user's settings will be shown
        }else{
            return view('user.show');
        }

    }
}
