<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $chirps = Chirp::with('user')
            ->latest()
            ->take(50)  // Limit to 50 most recent chirps
            ->get();

        return view('home', ['chirps' => $chirps]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'message' => 'required|string|max:255|min:5',
        ], [
            'message.required' => 'Please write something to chirp!',
            'message.max' => 'Chirps must be 255 characters or less.',
            'message.min' => 'It is too short! Chirps must be at least 5 characters long.',
            Rule::unique('chirps', 'message')->where(function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            }),
        ]);

        \App\Models\Chirp::create([
            'message' => $validated['message'],
            'user_id' => null,
        ]);

        return redirect('/')->with('success', 'Your chirp has been posted!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
