<div class="form-group">
    <a href="{{ action('CommunityController@show',[$com->id]) }}">{{ $com->name }}</a>
    @if (\lde\Community::find($com->id)->users()->get()->contains(\Illuminate\Support\Facades\Auth::user()))
        <button type="button" class="btn btn-danger pull-right">Disjoin</button>
    @else
        <button type="button" class="btn btn-primary pull-right">Join</button>
    @endif
</div>