@extends('layouts.app')

@section('navigation')
    <li><a href="{{ action('CommunityController@show',[$com->id]) }}">{{ $com->name }}</a></li>
@endsection

@section('menu-items')
    <li><a href="{{ action('CommunityController@createInitiative',array('id'=>$com->id)) }}">New Initiative</a></li>
@endsection

@section('content')
    <div class="container">
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
                            <th>Votes delegated in you</th>
                        </tr>
                        </thead>
                        @foreach($weights as $weight)
                            <tr>
                                <td>{{ $weight['type'] }}</td>
                                <td>{{ $weight['weight']-1 }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div id="ajax-delegations">
            @if(count($delegations)>0)
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2>My vote delegations</h2>
                        </div>
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Initiative type</th>
                                    <th>Delegating in</th>
                                    <th></th>
                                </tr>
                                </thead>
                                @foreach($delegations as $delegation)
                                    <tr>
                                        <td>{{ $delegation->initiativeType }}</td>
                                        <td>
                                            <a href="{{ action('UserController@show').'/'.$delegation->id }}">{{ $delegation->creator->name }}</a>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    data-communityId="{{ Auth::user()->wrapper($com->id)->id }}"
                                                    data-delegatedId="{{ $delegation->id }}"
                                                    data-initiativeTypeId="{{ $delegation->pivot->initiativeType_id }}"
                                                    data-pivot="{{ $delegation->pivot->id }}"
                                                    class="btn btn-danger pull-right btn-join undo">Undo delegation
                                            </button>
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

@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $('.undo').click(function () {
                undo($(this).data('pivot'), $(this).data('communityid'), $(this).data('delegatedid'), $(this).data('initiativetypeid'));
            });
        });
        function undo(pivot_id, community_id, delegated_id, initiativeType_id) {
            $.ajax({
                data: {
                    'id': pivot_id,
                    'community_id': community_id,
                    'delegated_id': delegated_id,
                    'initiativeType_id': initiativeType_id
                },
                url: "{{ url('com/ajax-undo') }}",
                type: 'post',
                beforeSend: function () {

                },
                success: function (data) {
                    $('#ajax-delegations').html(data);
                }
            });
        }
    </script>
@endsection