<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($itemId)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userId = Auth::id();

        $alreadyLiked = Like::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($alreadyLiked) {
            $alreadyLiked->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);
        }

        return redirect()->back();
    }
}