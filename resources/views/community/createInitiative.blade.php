@extends('layouts.app')

@section('first-menu-items')
    <li><a href="{{ action('UserController@show').'/'.Auth::user()->wrapper($id)->id }}">Me</a></li>
@endsection

@section('navigation')
    <li><a href="{{ action('CommunityController@show',[$id]) }}">{{ \lde\Community::find($id)->name }}</a></li>
@endsection

@section('content')
    <a href="{{ action('InitiativeController@create', array('community_id'=>$id)) }}">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 style="margin: 100px auto;text-align: center;">Initiative</h1>
                </div>
            </div>
        </div>
    </a>
    <a href="{{ action('MetainitiativeController@create', array('community_id'=>$id)) }}">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 style="margin: 100px auto;text-align: center;">Rule Initiative</h1>
                </div>
            </div>
        </div>
    </a>
@endsection