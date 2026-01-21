<style>
    #dark-comments-wrapper {
        background-color: #111 !important;
        color: #fff !important;
        padding: 30px 0;
        margin-top: 20px;
        border-top: 1px solid #222;
    }
    #dark-comments-wrapper h3 {
        color: #fff !important;
        border-bottom: 1px solid #333;
        padding-bottom: 15px;
        margin-bottom: 25px;
        font-weight: 600;
    }
    #dark-comments-wrapper .form-control {
        background-color: #222 !important;
        border: 1px solid #333 !important;
        color: #fff !important;
        border-radius: 4px;
    }
    #dark-comments-wrapper .form-control:focus {
        background-color: #2a2a2a !important;
        border-color: #fe8805 !important;
        box-shadow: none !important;
    }
    #dark-comments-wrapper .single-comment {
        background-color: #1a1a1a !important;
        border: 1px solid #333 !important;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
    }
    #dark-comments-wrapper .comment-user {
        color: #fe8805 !important;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
    }
    #dark-comments-wrapper .comment-date {
        font-size: 12px;
        color: #888 !important;
        float: right;
        font-weight: normal;
    }
    #dark-comments-wrapper .comment-text {
        color: #ddd !important;
        font-size: 14px;
        line-height: 1.6;
    }
    #dark-comments-wrapper .btn-primary {
        background: linear-gradient(90deg, #fe8805, #ff6b00) !important;
        border: none !important;
        color: #fff !important;
    }
    #dark-comments-wrapper .text-muted {
        color: #999 !important;
    }
    #dark-comments-wrapper a {
        color: #fe8805 !important;
    }
</style>

<div id="dark-comments-wrapper" class="vfx-item-ptb">
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
                <div class="alert alert-warning" style="background-color: #332b00; border-color: #665200; color: #ffda6a;">
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
