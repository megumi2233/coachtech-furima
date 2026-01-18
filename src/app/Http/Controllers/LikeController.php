<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($item_id)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user_id = Auth::id();

        $already_liked = Like::where('user_id', $user_id)->where('item_id', $item_id)->first();

        if ($already_liked) {
            $already_liked->delete();
        } else {
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
        }

        return redirect()->back();
    }
}
