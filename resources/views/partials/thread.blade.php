<?php
/*
 * This partial shows any conversation thread
 *
 * The thread object must be passed to the container view in $thread
 */
use lde\User;

$comments = $thread->comments;
foreach ($comments as $comment) {
    $comment->username = User::find($comment->user_id)->name;
}
?>
<div class="panel-body">
    <hr>
    @foreach($comments as $comment)
        <p>{{ $comment->created_at.' ' }}<strong>{{ $comment->username.':' }}</strong></p>
        <p>{{ $comment->text }}</p>
        <hr>
    @endforeach
</div>
<div class="panel-footer">
    {{ Form::open(array('url' => 'thread','role'=>'form')) }}
    <div class="form-group">
        {{ Form::label('text','Your comment:') }}
        {{ Form::textarea('text','',array('class'=>'form-control')) }}
    </div>
    {{ Form::hidden('thread_id',$thread->id) }}
    {{ Form::submit('Comment',array('class'=>'btn btn-primary')) }}
    {{ Form::close() }}
</div>
