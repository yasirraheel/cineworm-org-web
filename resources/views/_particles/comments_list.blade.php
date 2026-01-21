@if(count($comments) > 0)
    @foreach($comments as $comment)
    <div class="media mb-3" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 5px;">
        <div class="media-body">
            <h5 class="mt-0" style="color: #fe8805;">{{ $comment->user->name }} <small class="text-muted" style="font-size: 12px;">- {{ $comment->created_at->diffForHumans() }}</small></h5>
            <p class="text-white">{{ $comment->comment }}</p>
        </div>
    </div>
    @endforeach
@else
    <p class="text-muted">No comments yet. Be the first to comment!</p>
@endif
