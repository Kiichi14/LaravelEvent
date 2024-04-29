<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();

        return response()->json($events);
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

        $attributes = $request->all();

        $event = Event::create([
            'name' => $attributes['name'],
            'description' => $attributes['description'],
            'type' => $attributes['type'],
            'event_location' => $attributes['event_location']
        ]);

        $data = [
            'message' => 'Event has been created',
        ];

        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $event = Event::where("id", $id)->first();

        return response()->json($event);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();

        $event = Event::find($id)->update([
            'name' => $input['name'],
            'description' => $input['description'],
            'type' => $input['type'],
            'event_location' => $input['event_location']
        ]);

        return response()->json([
            'result' => "Votre événement a bien été mis a jour"
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
