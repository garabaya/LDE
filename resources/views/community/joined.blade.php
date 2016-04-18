@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="com-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>{{ $com->name }}</h2>
                    <h3>{{ $com->description }}</h3>
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
                                        <li>{{ $initiative->title }} ({{ $initiative->type->type }})</li>
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

                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="text-align: right;">
                    Members: {{ $com->users()->count() }}</div>
            </div>
        </div>

    </div>

@endsection
