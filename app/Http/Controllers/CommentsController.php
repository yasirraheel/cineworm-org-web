<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Settings;

class CommentsController extends Controller
{
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'msg' => 'Please login to comment']);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'post_id' => 'required',
            'post_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msg' => $validator->errors()->first()]);
        }

        $comment = new Comments();
        $comment->user_id = Auth::id();
        $comment->commentable_id = $request->post_id;
        $comment->commentable_type = $request->post_type; // e.g., 'App\Movies'
        $comment->comment = $request->comment;
        
        $auto_approve = getcong('comments_approval');
        $comment->status = $auto_approve ? 1 : 0;
        
        $comment->save();

        $msg = $auto_approve ? 'Comment published successfully' : 'Comment submitted for approval';

        return response()->json(['status' => 'success', 'msg' => $msg]);
    }
    
    public function getComments(Request $request)
    {
        $post_id = $request->post_id;
        $post_type = $request->post_type;
        
        $comments = Comments::where('commentable_id', $post_id)
                            ->where('commentable_type', $post_type)
                            ->where('status', 1)
                            ->with('user')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('_particles.comments_list', compact('comments'));
    }
}
