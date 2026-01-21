@if(count($comments) > 0)
    @foreach($comments as $comment)
    <div class="single-comment">
        <div class="comment-user">
            {{ $comment->user->name }}
            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="comment-text">{{ $comment->comment }}</div>
    </div>
    @endforeach
@else
    <p class="text-muted">No comments yet. Be the first to comment!</p>
@endif
