<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChirpController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $chirps = Chirp::with('user')
            ->latest()
            ->take(50)
            ->get();

        return view('home', ['chirps' => $chirps]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $messageRules = [
            'required',
            'string',
            'max:255',
            'min:5',
        ];

        if ($user) {
            $messageRules[] = Rule::unique('chirps', 'message')->where(function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            });
        }

        $validated = $request->validate(
            [
                'message' => $messageRules,
            ],
            [
                'message.required' => 'Please write something to chirp!',
                'message.max' => 'Chirps must be 255 characters or less.',
                'message.min' => 'It is too short! Chirps must be at least 5 characters long.',
                'message.unique' => 'You already posted that chirp.',
            ]
        );

        Chirp::create([
            'message' => $validated['message'],
            'user_id' => $user?->id,
        ]);

        return redirect('/')->with('success', 'Your chirp has been posted!');
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', compact('chirp'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Update
        $chirp->update($validated);

        return redirect('/')->with('success', 'Chirp updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        $chirp->delete();

        return redirect('/')->with('success', 'Chirp deleted!');
    }
}
