<?php

namespace lde\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use lde\Comment;
use lde\Http\Requests;

class ThreadController extends Controller
{
    public function comment(Request $request)
    {
        $comment = new Comment();
        $comment->user_id=Auth::user()->id;
        $comment->thread_id=$request->thread_id;
        $comment->text=$request->text;
        if ($comment->save()){
            return Redirect::back()->withErrors(array(
                'success' => ['New comment saved']));
        } else {
            return Redirect::back()->withErrors(array(
                'danger' => ['Something went wrong']));
        }
    }
}
