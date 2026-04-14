<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReactionController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'reactionable_type' => 'required|string|in:post,comment',
            'reactionable_id' => 'required|integer',
            'type' => 'required|string|in:like,love,haha,wow,sad,angry',
        ]);

        // Determine the model class
        $modelClass = $request->reactionable_type === 'post' ? Post::class : Comment::class;
        $reactionable = $modelClass::findOrFail($request->reactionable_id);

        // Find existing reaction
        $existing = Reaction::where('user_id', Auth::id())
            ->where('reactionable_type', $modelClass)
            ->where('reactionable_id', $reactionable->id)
            ->first();

        if ($existing && $existing->type === $request->type) {
            $existing->delete();
            $message = 'Reaction removed';
        } elseif ($existing) {
            $existing->update(['type' => $request->type]);
            $message = 'Reaction updated';
        } else {
            Reaction::create([
                'user_id' => Auth::id(),
                'reactionable_type' => $modelClass,
                'reactionable_id' => $reactionable->id,
                'type' => $request->type,
            ]);
            $message = 'Reaction added';
        }

        // Get updated counts
        $counts = Reaction::where('reactionable_type', $modelClass)
            ->where('reactionable_id', $reactionable->id)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        return response()->json(['message' => $message, 'counts' => $counts]);
    }
}