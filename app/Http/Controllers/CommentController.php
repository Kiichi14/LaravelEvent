<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function store(Request $request, $id)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'comment' => 'required|string',
            'rating' => 'integer',
        ]);

        $comment = Comment::create([
            'comment' => $validatedData['comment'],
            'rating' => $validatedData['rating'],
            'user_id' => $user->id,
            'event_id' => $id
        ]);

        return response()->json([
            'result' => 'votre commentaire a bien été enregistré'
        ], 201);

    }

}
