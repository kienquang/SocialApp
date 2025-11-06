<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostVoteController extends Controller
{
    /**
     * Hàm private để xử lý logic vote.
     * @param Post $post
     * @param int $voteValue (1 cho upvote, -1 cho downvote)
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleVote(Post $post, $voteValue)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Lấy pivot data (dữ liệu vote) của user này cho post này
        $existingVote = $user->votedPosts()->where('post_id', $post->id)->first();

        $message = '';
        $newVote = null;

        if ($existingVote) {
            // User đã vote bài này trước đây
            if ($existingVote->pivot->vote == $voteValue) {
                // --- KỊCH BẢN 1: Bỏ vote ---
                // User nhấn upvote 2 lần -> hủy upvote
                // User nhấn downvote 2 lần -> hủy downvote
                $user->votedPosts()->detach($post->id);
                $message = 'Đã hủy vote';
                $newVote = 0; // 0 nghĩa là không vote
            } else {
                // --- KỊCH BẢN 2: Đổi vote ---
                // User đã upvote, giờ nhấn downvote -> đổi thành downvote
                // User đã downvote, giờ nhấn upvote -> đổi thành upvote
                $user->votedPosts()->updateExistingPivot($post->id, ['vote' => $voteValue]);
                $message = 'Đã thay đổi vote';
                $newVote = $voteValue;
            }
        } else {
            // --- KỊCH BẢN 3: Vote mới ---
            // User chưa vote bài này bao giờ
            $user->votedPosts()->attach($post->id, ['vote' => $voteValue]);
            $message = 'Đã vote thành công';
            $newVote = $voteValue;
        }

        // Tải lại điểm số (tính toán lại)
        // Chúng ta dùng withSum để tính tổng cột 'vote'
        $newScore = $post->voters()->sum('vote'); //bởi vì có pivot ở model post nên nó vẫn chạy được trên sum còn withsum thì không

        return response()->json([
            'message' => $message,
            'vote_score' => $newScore,
            'user_vote' => $newVote,
        ]);
    }

    /**
     * Xử lý Upvote.
     */
    public function upvote(Post $post)
    {
        return $this->handleVote($post, 1);
    }

    /**
     * Xử lý Downvote.
     */
    public function downvote(Post $post)
    {
        return $this->handleVote($post, -1);
    }
}

