@extends('layouts.app')

@section('navigation')
    <li><a href="{{ action('CommunityController@show',[$community->id]) }}">{{ $community->name }}</a></li>
@endsection

@section('first-menu-items')
    <li><a href="{{ action('UserController@show').'/'.Auth::user()->wrapper($community->id)->id }}">Me</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>Create initiative for: {{ $community->name }}</h2>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url'=>url('initiative'))) }}
                    {{ Form::hidden('community_id',$community->id) }}
                    <div class="form-group">
                        {{ Form::label('initiativeType_id', "Select the initiative's type",array('class'=>'control-label')) }}
                        {{ Form::select('initiativeType_id', $types,null,array('class'=>'form-control')) }}
                    </div>
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {{ Form::label('title', 'Title',array('class'=>'control-label')) }}
                        {{ Form::text('title',old('title'),array('class'=>'form-control')) }}
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {{ Form::label('description', 'Description',array('class'=>'control-label')) }}
                        {{ Form::textarea('description',old('description'),array('class'=>'form-control')) }}
                    </div>
                    {{ Form::submit('Create initiative',array('class'=>'btn btn-primary')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection