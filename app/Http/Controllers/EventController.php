<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        // Validation des données entrantes
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required',
            'event_location' => 'required|string',
            'event_date' => 'required|date_format:Y-m-d',
            'event_city' => 'required|integer'
        ]);

        // Formater la date au format Y-m-d
        $formattedDate = Carbon::createFromFormat('Y-m-d', $validatedData['event_date'])->toDateString();

        $event = Event::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
            'event_location' => $validatedData['event_location'],
            'event_date' => $formattedDate,
        ]);

        $event->locations()->attach($validatedData['event_city'], [
            'event_id' => $event->id,
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
        $event = Event::with('locations')->where("id", $id)->first();

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
            'event_location' => $input['event_location'],
            'event_date' => $input['event_date'],
        ]);

        return response()->json([
            'result' => "Votre événement a bien été mis a jour"
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
    }
}
