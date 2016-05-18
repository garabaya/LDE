@extends('layouts.app')

@section('navigation')
    <li><a href="{{ action('CommunityController@show',[$community->id]) }}">{{ $community->name }}</a></li>
@endsection

@section('first-menu-items')
    <li><a href="{{ action('UserController@show').'/'.Auth::user()->wrapper($community->id)->id }}">Me</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>Create rule initiative for: {{ $community->name }}</h2>
                </div>
                <div class="panel-body">
                    {{ Form::open(array('url'=>url('metainitiative'))) }}
                    {{ Form::hidden('community_id',$community->id) }}
                    <div class="form-group">
                        {{ Form::label('community_rule_id', 'Select the rule you want to change',array('class'=>'control-label')) }}
                        {{ Form::select('community_rule_id', $rules,null,array('class'=>'form-control')) }}
                    </div>
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        {{ Form::label('title', 'Title',array('class'=>'control-label')) }}
                        {{ Form::text('title',old('title'),array('class'=>'form-control')) }}
                    </div>
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        {{ Form::label('description', 'Description',array('class'=>'control-label')) }}
                        {{ Form::textarea('description',old('description'),array('class'=>'form-control')) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label(null,'Value:',array('class'=>'control-label')) }}
                        <span class="control-label" id="val"></span>
                    </div>
                    <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                        {{ Form::label('value', 'Proposed value',array('class'=>'control-label')) }}
                        <div id="value-container">
                        </div>

                    </div>
                    {{ Form::submit('Create rule initiative',array('class'=>'btn btn-primary')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            enviar();
            $('#community_rule_id').change(function () {
                enviar();
            });
        });
        function enviar() {
            $.ajax({
                data: 'id=' + $('#community_rule_id').val(),
                url: "{{ url('metainitiative/ruleSelected') }}",
                type: 'post',
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.type == 'boolean') {
                        $('#value-container').html('{{ Form::select('value', array('true'=>'yes','false'=>'no'),null,array('class'=>'form-control')) }}');
                        if (data.value == 'true') $('#val').text('yes');
                        else $('#val').text('no');
                    } else {
                        $('#value-container').html('{{ Form::text('value',null,array('class'=>'form-control','id'=>'value')) }}');
                        if (data.type=='numeric'){
                            $('#value').attr('type','number');
                        }
                        $('#val').text(data.value);
                    }

                }
            });
        }
    </script>
@endsection