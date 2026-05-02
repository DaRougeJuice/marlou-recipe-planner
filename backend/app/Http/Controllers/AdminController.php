<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Favorite;

class AdminController extends Controller
{
    public function users() {
        return User::select('id','name','email','role','created_at')->get();
    }

    public function deleteUser($id) {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function recipes() {
        return Favorite::with('user:id,name,email')->latest()->get();
    }

    public function deleteRecipe($id) {
        Favorite::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}