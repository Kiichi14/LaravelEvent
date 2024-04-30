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
        $events = Event::with('locations')->get();

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
            'date' => 'required|date_format:Y-m-d',
            'event_city' => 'required|integer',
            'theater' => 'required|string',
            'place_number' => 'required|integer'
        ]);

        // Formater la date au format Y-m-d
        $formattedDate = Carbon::createFromFormat('Y-m-d', $validatedData['date'])->toDateString();

        $event = Event::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
        ]);

        $event->locations()->attach($validatedData['event_city'], [
            'theater' => $validatedData['theater'],
            'place_number' => $validatedData['place_number'],
            'date' => $validatedData['date']
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

    public function newAppearance(Request $request, $id)
    {

        // Validation des données entrantes
        $validatedData = $request->validate([
            'event_city' => 'required|integer',
            'theater' => 'required|string',
            'place_number' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $event = Event::findOrFail($id);

        $event->locations()->attach($validatedData['event_city'], [
            'theater' => $validatedData['theater'],
            'place_number' => $validatedData['place_number'],
            'date' => $validatedData['date']
        ]);

        $data = [
            'message' => 'your new event has been created',
        ];

        return response()->json($data, 201);
    }
}
