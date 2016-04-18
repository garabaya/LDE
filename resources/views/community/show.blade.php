@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $com->name }}</h2>
                    <h3>{{ $com->description }}</h3>
                </div>
                <div class="panel-body">
                    <h4>
                        Rules
                    </h4>
                    <ul>
                        @foreach( $com->rules()->get() as $rule)
                            <li>
                                {{ $rule->description }}: @if ($rule->type=='boolean')
                                    @if ($rule->pivot->value=='true') yes
                                    @else no
                                    @endif
                                @else{{ $rule->pivot->value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <form id="form-join" method="POST" action="{{ action('CommunityController@join') }}">
                        {!! csrf_field() !!}
                        <input type="hidden" name="id" value="{{ $com->id }}">
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Join</button>
                        </div>
                    </form>
                </div>
                <div class="panel-footer" style="text-align: right;">Members: {{ $com->users()->count() }}</div>
            </div>
        </div>
    </div>

@endsection