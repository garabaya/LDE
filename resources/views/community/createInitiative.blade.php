@extends('layouts.app')

@section('content')
    <a href="#">
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