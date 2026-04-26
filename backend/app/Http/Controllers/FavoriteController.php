<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id)->get();
        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $request->validate([
            'meal_id'    => 'required|string',
            'meal_name'  => 'required|string',
            'meal_thumb' => 'required|string',
        ]);

        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('meal_id', $request->meal_id)
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Already saved'], 409);
        }

        $favorite = Favorite::create([
            'user_id'    => $request->user()->id,
            'meal_id'    => $request->meal_id,
            'meal_name'  => $request->meal_name,
            'meal_thumb' => $request->meal_thumb,
        ]);

        return response()->json($favorite, 201);
    }

    public function destroy(Request $request, $mealId)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('meal_id', $mealId)
            ->delete();

        return response()->json(['message' => 'Removed']);
    }
}