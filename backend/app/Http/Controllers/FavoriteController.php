<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Favorite::where('user_id', $request->user()->id)->get()
        );
    }

    public function store(Request $request)
    {
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('meal_id', $request->meal_id)
            ->first();

        if ($exists) return response()->json($exists, 409);

        $favorite = Favorite::create([
            'user_id'    => $request->user()->id,
            'meal_id'    => $request->meal_id,
            'meal_name'  => $request->meal_name,
            'meal_thumb' => $request->meal_thumb,
        ]);

        return response()->json($favorite, 201);
    }

    public function destroy(Request $request, $id)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['message' => 'Removed']);
    }
}