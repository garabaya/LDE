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
            $coms_delegated = $wrapper->delegateIn()->get();
            foreach($coms_delegated as $com_delegated){
                $com_delegated->initiativeType=InitiativeType::find($com_delegated->pivot->initiativeType_id)->type;
            }
            $weights = array();
            foreach(InitiativeType::get() as $initiativeType){
                $type_info = array();
                $type_info['id']=$initiativeType->id;
                $type_info['type']=$initiativeType->type;
                $type_info['weight']=Delegation::user_weight($wrapper_id,$initiativeType->id);
                $type_info['isDelegatingInMe']=true;
                array_push($weights,$type_info);
            }

            //TODO once the voting system is finished, include here a list of pending votations
            //TODO i.e. a list of still opened initiatives that the user hasn't vote yet
            //I want to see my own user information and settings as a community member
            if ($me->wrapper($community->id)==$wrapper){
                return view('community.me',[
                    'weights' => $weights,
                    'com' => $community,
                    'delegations' => $coms_delegated
                ]);
            //I want to see the information community member
            }else{
                $metainitiatives = $wrapper->metapropose($community->id)->get();
                $initiatives = $wrapper->propose($community->id)->get();
                $allInitiatives=$metainitiatives->merge($initiatives)->sortByDesc('created_at');
                return view('community.user',[
                    'wrapper' => $wrapper,
                    'weights' => $weights,
                    'com' => $community,
                    'delegations' => $coms_delegated,
                    'initiatives' => $allInitiatives
                ]);
            }
        //TODO general user's settings will be shown
        }else{
            return view('user.show');
        }

    }

}
