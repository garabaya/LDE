<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $member->id }}">{{ $member->creator->name }}</a>
            <a class="pull-right" href="{{ action('UserController@show').'/'.$member->id }}">Go</a>
        </h2>
    </div>
    <div id="collapse{{ $member->id }}" class="panel-collapse collapse">
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Initiative type</th>
                    <th></th>
                </tr>
                </thead>
                @foreach($initiativeTypes as $initiativeType)
                    <tr>
                        <td>{{ $initiativeType->type }}</td>
                        <td>
                            @if (Auth::user()->wrapper($com->id)->isInDelegatingLine($member->id,$initiativeType->id))
                                You can't delegate in {{ $member->creator->name }}. You are in his
                                delegations line.
                            @elseif(Auth::user()->wrapper($com->id)->delegation($initiativeType->id)==null)
                                <button type="button"
                                        data-delegatedid="{{ $member->id }}"
                                        data-initiativetypeid="{{ $initiativeType->id }}"
                                        data-comid="{{ $com->id }}"
                                        class="btn btn-primary pull-right btn-join btn-delegate">
                                    Delegate my vote
                                </button>
                            @elseif(Auth::user()->wrapper($com->id)->delegation($initiativeType->id)->delegated_id==$member->id)
                                <button type="button"
                                        data-delegatedid="{{ $member->id }}"
                                        data-initiativetypeid="{{ $initiativeType->id }}"
                                        data-comid="{{ $com->id }}"
                                        class="btn btn-danger pull-right btn-join btn-delegate">Undo
                                    delegation
                                </button>
                            @else
                                <button type="button"
                                        data-delegatedid="{{ $member->id }}"
                                        data-initiativetypeid="{{ $initiativeType->id }}"
                                        data-comid="{{ $com->id }}"
                                        class="btn btn-primary pull-right btn-join btn-delegate">
                                    Delegate my vote
                                </button>
                                <div class="pull-right" style="clear: both">(Now in <a
                                            href="{{ action('UserController@show').'/'.Auth::user()->wrapper($com->id)->delegation($initiativeType->id)->delegated_id }}">{{ \lde\Community::find(Auth::user()->wrapper($com->id)->delegation($initiativeType->id)->delegated_id)->creator->name }}
                                        )</a></div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>