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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $wrapper->creator->name }}</h2>
                </div>
                <div class="panel panel-body">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2>Vote weights</h2>
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Initiative type</th>
                                        <th>Votes delegated in {{ $wrapper->creator->name }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    @foreach($weights as $weight)
                                        <tr>
                                            <td>{{ $weight['type'] }}</td>
                                            <td>{{ $weight['weight']-1 }}</td>
                                            <td>
                                                @if (Auth::user()->wrapper($com->id)->isInDelegatingLine($wrapper->id,$weight['id']))
                                                    You can't delegate in {{ $wrapper->creator->name }}. You are in his
                                                    delegations line.
                                                @elseif(Auth::user()->wrapper($com->id)->delegation($weight['id'])==null)
                                                    <button type="button"
                                                            data-delegatedid="{{ $wrapper->id }}"
                                                            data-initiativetypeid="{{ $weight['id'] }}"
                                                            data-comid="{{ $com->id }}"
                                                            class="btn btn-primary pull-right btn-join btn-delegate">
                                                        Delegate my vote
                                                    </button>
                                                @elseif(Auth::user()->wrapper($com->id)->delegation($weight['id'])->delegated_id==$wrapper->id)
                                                    <button type="button"
                                                            data-delegatedid="{{ $wrapper->id }}"
                                                            data-initiativetypeid="{{ $weight['id'] }}"
                                                            data-comid="{{ $com->id }}"
                                                            class="btn btn-danger pull-right btn-join btn-delegate">Undo
                                                        delegation
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            data-delegatedid="{{ $wrapper->id }}"
                                                            data-initiativetypeid="{{ $weight['id'] }}"
                                                            data-comid="{{ $com->id }}"
                                                            class="btn btn-primary pull-right btn-join btn-delegate">
                                                        Delegate my vote
                                                    </button>
                                                    <div class="pull-right" style="clear: both">(Now in <a
                                                                href="{{ action('UserController@show').'/'.Auth::user()->wrapper($com->id)->delegation($weight['id'])->delegated_id }}">{{ \lde\Community::find(Auth::user()->wrapper($com->id)->delegation($weight['id'])->delegated_id)->creator->name }}
                                                            )</a></div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2>Vote delegations</h2>
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Initiative type</th>
                                        <th>Delegating in</th>
                                    </tr>
                                    </thead>
                                    @foreach($delegations as $delegation)
                                        <tr>
                                            <td>{{ $delegation->initiativeType }}</td>
                                            <td>
                                                <a href="{{ action('UserController@show').'/'.$delegation->id }}">{{ $delegation->creator->name }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    @if ($initiatives->count()>0)
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h2>Initiatives</h2>
                                </div>
                                <div class="panel-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Initiative</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        @foreach($initiatives as $initiaitive)
                                            <tr>
                                                <td>
                                                    <a href="{{ get_class($initiaitive)=='lde\MetaInitiative'?action('MetainitiativeController@show',array('id'=>$initiaitive->id)):action('InitiativeController@show',array('id'=>$initiaitive->id)) }}">{{ $initiaitive->title }}</a>
                                                </td>
                                                <td>
                                                    @if (get_class($initiaitive)=='lde\MetaInitiative')
                                                        Rule initiative
                                                    @else
                                                        {{ $initiaitive->type['type'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $initiaitive->created_at }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <form id="form-delegate" method="POST" action="{{ action('CommunityController@delegate') }}">
        {!! csrf_field() !!}
        <input type="hidden" name="delegated_id" id="input_delegatedId">
        <input type="hidden" name="initiativeType_id" id="input_initiativeTypeId">
        <input type="hidden" name="community_id" id="input_comId">
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.btn-delegate').click(function () {
                var delegatedId = $(this).data('delegatedid');
                var initiativeTypeId = $(this).data('initiativetypeid');
                var comId = $(this).data('comid');
                var form = $('#form-delegate');
                var input_delegatedId = $('#input_delegatedId');
                var input_initiativeTypeId = $('#input_initiativeTypeId');
                var input_comId = $('#input_comId');
                input_delegatedId.val(delegatedId);
                input_initiativeTypeId.val(initiativeTypeId);
                input_comId.val(comId);
                form.submit();
            });
        });
    </script>
@endsection