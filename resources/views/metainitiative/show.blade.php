@extends('layouts.app')

@section('menu-items')
    <li><a href="#">New Initiative</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="com-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $metainitiative->title }}</h2>
                    <div style="width: 100%;" class="container">
                        <div class="col-md-6">
                            <ul>
                                <li>Rule: {{ $rule->description }}</li>
                                <li>Value: @if ($rule->type=='boolean')
                                        @if ($rule->value=='true') yes
                                        @else no
                                        @endif
                                    @else{{ $rule->value }}
                                    @endif</li>
                                <li>Proposed value: @if ($rule->type=='boolean')
                                        @if ($rule->newValue=='true') yes
                                        @else no
                                        @endif
                                    @else{{ $rule->newValue }}
                                    @endif</li>
                                <li>Proposer: {{ $rule->user }}</li>
                            </ul>
                            Description:
                            <p>{{ $metainitiative->description }}</p>
                        </div>
                        <div class="col-md-6">
                            Supports/needed: {{ $metainitiative->supporters }}/{{ $metainitiative->needed }}<br>
                            Expire date: {{ $metainitiative->expireDate }}<br>
                        </div>
                        @if ($supporting)
                            <button style="float:left;clear: both;" disabled type="button" class="btn btn-primary btn-join">Supporting</button>
                        @else
                            <button style="float:left;clear: both;" type="button" data-metainitiative="{{ $metainitiative->id }}" class="btn btn-primary btn-support">Support this Initiative</button>
                        @endif
                    </div>
                </div>
                <div class="panel-body">
                    <hr>
                    @foreach($comments as $comment)
                        <p>{{ $comment->created_at.' ' }}<strong>{{ $comment->username.':' }}</strong></p>
                        <p>{{ $comment->text }}</p>
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <form id="form-support" method="POST" action="{{ action('MetainitiativeController@support') }}">
        {!! csrf_field() !!}
        <input type="hidden" name="id" id="metainitiative-id">
    </form>

@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.btn-support').click(function () {
                var metainitiative = $(this).data('metainitiative');
                var form = $('#form-support');
                var input = $('#metainitiative-id');
                input.val(metainitiative);
                form.submit();
            });
        });
    </script>
@endsection