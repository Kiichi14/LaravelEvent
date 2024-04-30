<?php

use App\Models\Event;
use App\Models\EventLocation;
use App\Models\Location;

use function Pest\Laravel\withoutExceptionHandling;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

test('should return a simple list of all event', function() {

    withoutExceptionHandling();

    $eventsCount = 5;

    $events = Event::factory()->count($eventsCount)->create();

    $response = $this->get('event')->assertStatus(200);

    $response->assertJsonCount($events->count($eventsCount));

});

test('should return a event detail', function() {
    withoutExceptionHandling();
    $event = Event::factory()->create();

    $response = $this->get('event/'.$event->id)->assertStatus(200);

    $eventDetails = $response->json();

    $this->assertEquals($event->id, $eventDetails['id']);
});

test('should create an event', function() {
    withoutExceptionHandling();

    $data = Event::factory()->raw();

    $location = Location::factory()->create();

    $data['event_date'] = $data['event_date']->format('Y-m-d');

    $data['event_city'] = $location->id;

    $data['theater'] = 'theater';

    $data['place_number'] = 250;

    $response = $this->post('event', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('events', [
        'name' => $data['name'],
        'description' => $data['description'],
        'type' => $data['type'],
        'event_location' => $data['event_location'],
        'event_date' => $data['event_date']
    ]);

    $event = Event::where('name', $data['name'])->firstOrFail();
    $this->assertTrue($event->locations->contains($location->id));
});

test('should update an event', function() {
    withoutExceptionHandling();
    $event = Event::factory()->create();

    $newEventData = [
        'name' => 'Nouveau nom d\'événement',
        'description' => 'Nouvelle description d\'événement',
        'type' => "concert",
        'event_location' => 'Nouveau lieu d\'événement',
        'event_date' => '2024-05-01'
    ];

    $response = $this->put("event/{$event->id}", $newEventData);

    $response->assertStatus(200);

    $updatedEvent = Event::find($event->id);

    expect($updatedEvent->name)->toBe($newEventData['name']);
    expect($updatedEvent->description)->toBe($newEventData['description']);
    expect($updatedEvent->type)->toBe($newEventData['type']);
    expect($updatedEvent->event_location)->toBe($newEventData['event_location']);
    expect($updatedEvent->event_date)->toBe($newEventData['event_date']);
});

test('should delete an event', function() {

    $event = Event::factory()->create();

    $response = $this->delete("event/{$event->id}")->assertStatus(200);

    $this->assertDatabaseCount('events', 0);
});

test('event should have city and country', function() {

    $location = Location::factory()->create();

    $event = Event::factory()->create();

    $theater = 'Some Theater';

    $place = 250;

    $event->locations()->attach($location->id, attributes: [
        'theater' => $theater,
        'place_number' => $place
    ]);

    $this->assertTrue($event->locations->count() > 0);

    $this->assertInstanceOf(Location::class, $event->locations()->first());
});

test('should add a new appearance for event', function () {
    withoutExceptionHandling();
    $location = Location::factory()->create();
    $event = Event::factory()->create();

    $theater = 'New Theater';
    $place = 123;
    $eventCityId = $location->id;

    $data = [
        'event_city' => $eventCityId,
        'theater' => $theater,
        'place_number' => $place
    ];

    $response = $this->post("event/newdate/{$event->id}", $data);

    $response->assertStatus(201)
             ->assertJson([
                 'message' => 'your new event has been created'
             ]);

    // Vérifier que la nouvelle apparition a été ajoutée avec succès
    $this->assertDatabaseHas('event_location', [
        'event_id' => $event->id,
        'location_id' => $eventCityId,
        'theater' => $theater,
        'place_number' => $place
    ]);
});
