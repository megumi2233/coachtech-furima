<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();

        $soldItems = Item::where('user_id', $user->id)->get();

        return view('mypage.profile', compact('user', 'profile', 'purchasedItems', 'soldItems'));
    }

    public function edit()
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)->first();

        return view('mypage.edit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $isFirstTime = Profile::where('user_id', $user->id)->doesntExist();

        $user->name = $request->name;
        $user->save();

        $profileData = [
            'zipcode' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building,
        ];

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profileData['avatar_url'] = $path;
        }

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        if ($isFirstTime) {
            return redirect('/');
        }

        return redirect('/mypage');
    }

    public function editAddress($itemId)
    {
        return view('purchase.address', ['itemId' => $itemId]);
    }

    public function updateAddress(AddressRequest $request, $itemId)
    {
        $user = Auth::user();

        $user->profile->update([
            'zipcode' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building_name' => $request->input('building'),
        ]);

        return redirect('/purchase/' . $itemId);
    }
}
