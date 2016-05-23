<?php

namespace lde\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use lde\Community;
use lde\Http\Requests;
use Illuminate\Http\Request;
use lde\Initiative;
use lde\MetaInitiative;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * coms: All communities
     * joined: Communities in which the user has joined
     *
     * In a real case of use some type of filter and order should be applied to results (e.g. popular communities)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $communities = Community::popular()->get();
//        $communities = Community::where('type','general')->get();
        $joined = Auth::user()->communities()->get();
        return view('home', [
            'coms' => $communities,
            'joined' => $joined
        ]);
    }
}
