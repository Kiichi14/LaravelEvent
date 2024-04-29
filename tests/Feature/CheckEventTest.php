<?php

use App\Models\Event;
use Illuminate\Support\Facades\Schema;
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

    $response = $this->post('/event/create', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('events', [
        'name' => $data['name'],
        'description' => $data['description'],
        'type' => $data['type'],
        'event_location' => $data['event_location']
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
    ];

    $response = $this->put("/event/update/{$event->id}", $newEventData);

    $response->assertStatus(200);

    $updatedEvent = Event::find($event->id);

    expect($updatedEvent->name)->toBe($newEventData['name']);
    expect($updatedEvent->description)->toBe($newEventData['description']);
    expect($updatedEvent->type)->toBe($newEventData['type']);
    expect($updatedEvent->event_location)->toBe($newEventData['event_location']);
});
