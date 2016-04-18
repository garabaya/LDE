<div class="form-group">
    <a href="{{ action('CommunityController@show',[$com->id]) }}">{{ $com->name }}</a>
    @if (\lde\Community::find($com->id)->users()->get()->contains(\Illuminate\Support\Facades\Auth::user()))
        <button type="button" data-com="{{ $com->id }}" class="btn btn-danger pull-right btn-join">Disjoin</button>
    @else
        <button type="button" data-com="{{ $com->id }}" class="btn btn-primary pull-right btn-join">Join</button>
    @endif
</div>