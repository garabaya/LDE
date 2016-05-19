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
        <div class="panel-group" id="accordion">
            @foreach($members as $member)
                @if ($member->id!=Auth::user()->wrapper($com->id)->id)
                    @include('partials.member')
                @endif
            @endforeach
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