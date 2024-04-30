<?php

use App\Models\Event;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\withoutExceptionHandling;

uses(RefreshDatabase::class);

test('should return a simple list of all event', function() {

    $eventsCount = 5;

    $events = Event::factory()->count($eventsCount)->create();

    $response = $this->get('/event')->assertStatus(200);

    $response->assertJsonCount($events->count($eventsCount));

});

test('should return a event detail', function() {

    $event = Event::factory()->create();

    $response = $this->get('/event/detail/'.$event->id)->assertStatus(200);

    $eventDetails = $response->json();

    $this->assertEquals($event->id, $eventDetails['id']);
});

test('should create an event', function() {
    withoutExceptionHandling();

    $data = Event::factory()->raw();

    $data['event_date'] = $data['event_date']->format('Y-m-d');

    $response = $this->post('/event/create', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('events', [
        'name' => $data['name'],
        'description' => $data['description'],
        'type' => $data['type'],
        'event_location' => $data['event_location'],
        'event_date' => $data['event_date']
    ]);
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

    $response = $this->put("/event/update/{$event->id}", $newEventData);

    $response->assertStatus(200);

    $updatedEvent = Event::find($event->id);

    expect($updatedEvent->name)->toBe($newEventData['name']);
    expect($updatedEvent->description)->toBe($newEventData['description']);
    expect($updatedEvent->type)->toBe($newEventData['type']);
    expect($updatedEvent->event_location)->toBe($newEventData['event_location']);
    expect($updatedEvent->event_date)->toBe($newEventData['event_date']);
});

test('event should have a city and country', function() {

    $location = Location::factory()->create();

    $event = Event::factory()->create(['location_id' => $location->id]);

    $this->assertInstanceOf(Location::class, $event->location);
});
