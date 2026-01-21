<div class="comments-section vfx-item-ptb">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="vfx-item-section">
                    <h3>Comments</h3>
                </div>

                @if(Auth::check())
                <div class="comment-form">
                    <form id="comment_form">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post_id }}">
                        <input type="hidden" name="post_type" value="{{ $post_type }}">
                        <div class="form-group">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Write your comment here..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Submit Comment</button>
                    </form>
                    <div id="comment_msg" class="mt-2"></div>
                </div>
                @else
                <div class="alert alert-warning">
                    Please <a href="{{ URL::to('login') }}">login</a> to post a comment.
                </div>
                @endif

                <div id="comments_list" class="mt-4">
                    <!-- Comments will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadComments();

        var commentForm = document.getElementById('comment_form');
        if(commentForm){
            commentForm.addEventListener('submit', function(e){
                e.preventDefault();
                var formData = new FormData(this);

                fetch('{{ URL::to("comments/add") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    var msgDiv = document.getElementById('comment_msg');
                    if(data.status == 'success'){
                        msgDiv.innerHTML = '<div class="text-success">'+data.msg+'</div>';
                        this.reset();
                        loadComments();
                    } else {
                        msgDiv.innerHTML = '<div class="text-danger">'+data.msg+'</div>';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
    });

    function loadComments(){
        var postId = '{{ $post_id }}';
        var postType = {!! json_encode($post_type) !!};

        fetch('{{ URL::to("comments/get") }}?post_id='+postId+'&post_type='+encodeURIComponent(postType))
        .then(response => response.text())
        .then(html => {
            document.getElementById('comments_list').innerHTML = html;
        });
    }
</script>
