@extends('layouts.app')

@section('menu-items')
    <li><a href="{{ action('CommunityController@createInitiative',array('id'=>$community_id)) }}">New Initiative</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="com-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $initiative->title }}</h2>
                    <div style="width: 100%;" class="container">
                        <div class="col-md-6">
                            Proposer: {{ $initiative->user->name }}<br>
                            Description:
                            <p>{{ $initiative->description }}</p>
                        </div>
                        <div class="col-md-6">
                            Supports/needed: {{ $initiative->supporters }}/{{ $initiative->needed }}<br>
                            Expire date: {{ $initiative->expireDate }}<br>
                        </div>
                        @if ($supporting)
                            <button style="float:left;clear: both;" disabled type="button" class="btn btn-primary btn-join">Supporting</button>
                        @else
                            <button style="float:left;clear: both;" type="button" data-initiative="{{ $initiative->id }}" class="btn btn-primary btn-support">Support this Initiative</button>
                        @endif
                    </div>
                </div>
                @include('partials.thread')
            </div>
        </div>
    </div>

    <form id="form-support" method="POST" action="{{ action('InitiativeController@support') }}">
        {!! csrf_field() !!}
        <input type="hidden" name="id" id="initiative-id">
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.btn-support').click(function () {
                var initiative = $(this).data('initiative');
                var form = $('#form-support');
                var input = $('#initiative-id');
                input.val(initiative);
                form.submit();
            });
        });
    </script>
@endsection