@extends('layouts.app')

@section('navigation')
    <li><a href="{{ action('CommunityController@show',[$com->id]) }}">{{ $com->name }}</a></li>
@endsection

@section('first-menu-items')
    <li><a href="{{ action('UserController@show').'/'.Auth::user()->wrapper($com->id)->id }}">Me</a></li>
@endsection

@section('menu-items')
    <li><a href="{{ action('CommunityController@createInitiative',array('id'=>$com->id)) }}">New Initiative</a></li>
@endsection

@section('content')

    <div class="container">
        <div class="com-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $com->name }}</h2>
                    <h3>{{ $com->description }}</h3>
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
                    <button type="button" data-com="{{ $com->id }}" class="btn btn-danger btn-join">Disjoin</button>
                </div>
                <div class="panel-body">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2>Initiatives</h2>
                            </div>
                            <div class="panel-body">
                                <ul>
                                    @foreach($com->scopedBy as $initiative)
                                        @if ($initiative->checkSupport()==null)
                                            <li>
                                                <a href="{{ action('InitiativeController@show',[$initiative->id]) }}"> {{ $initiative->title }}
                                                    ({{ $initiative->type->type }})</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2>Rule initiatives</h2>
                            </div>
                            <div class="panel-body">
                                <ul>
                                    @foreach($com->metaInitiatives() as $initiative)
                                        @if ($initiative->checkSupport()==null)
                                            <li>
                                                <a href="{{ action('MetainitiativeController@show',[$initiative->id]) }}">{{ $initiative->title }}
                                                    ({{ $initiative->value }})</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="text-align: right;">
                    <a href="{{ action('CommunityController@members',[$com->id]) }}">Members: {{ $com->users()->count() }}</a>
                </div>
            </div>
        </div>

    </div>

    <form id="form-join" method="POST" action="{{ action('CommunityController@join') }}">
        {!! csrf_field() !!}
        <input type="hidden" name="id" id="com-id">
    </form>

@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.btn-join').click(function () {
                var com = $(this).data('com');
                var form = $('#form-join');
                var input = $('#com-id');
                input.val(com);
                form.submit();
            });
        });
    </script>
@endsection
