@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">My communities</div>

                    <div class="panel-body">
                        @foreach($joined->all() as $com)
                            @include('partials.comOverview')
                        @endforeach
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Popular communities</div>

                    <div class="panel-body">
                        @foreach($coms->all() as $com)
                            @include('partials.comOverview')
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection
