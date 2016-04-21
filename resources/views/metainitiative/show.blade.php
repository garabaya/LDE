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
                    <div class="container">
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
                            {{-- TODO supports count --}}
                            Supports/needed: /{{ $metainitiative->needed }}<br>
                            Expire date: {{ $metainitiative->expireDate }}
                            {{-- TODO support button --}}
                        </div>
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

@endsection