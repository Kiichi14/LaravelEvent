<?php

use App\Models\Event;
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

    $data['event_city'] = $location->id;

    $data['theater'] = 'theater';

    $data['place_number'] = 250;

    $data['date'] = "2024-10-22";

    $response = $this->post('event', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('events', [
        'name' => $data['name'],
        'description' => $data['description'],
        'type' => $data['type'],
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
    ];

    $response = $this->put("event/{$event->id}", $newEventData);

    $response->assertStatus(200);

    $updatedEvent = Event::find($event->id);

    expect($updatedEvent->name)->toBe($newEventData['name']);
    expect($updatedEvent->description)->toBe($newEventData['description']);
    expect($updatedEvent->type)->toBe($newEventData['type']);
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

    $date = new \DateTime('now');

    $event->locations()->attach($location->id, attributes: [
        'theater' => $theater,
        'place_number' => $place,
        'date' => $date
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
    $newDate = "2024-10-22";
    $eventCityId = $location->id;

    $data = [
        'event_city' => $eventCityId,
        'theater' => $theater,
        'place_number' => $place,
        'date' => $newDate
    ];

    $response = $this->post("event/newdate/{$event->id}", $data);

    $response->assertStatus(201)
             ->assertJson([
                 'message' => 'your new event has been created'
             ]);

    $this->assertDatabaseHas('event_location', [
        'event_id' => $event->id,
        'location_id' => $eventCityId,
        'theater' => $theater,
        'place_number' => $place,
        'date' => $newDate
    ]);
});

test('should filter events by city or type', function() {
    withoutExceptionHandling();
    $locationA = Location::factory()->create(['city' => 'Paris']);
    $locationB = Location::factory()->create(['city' => 'Barcelone']);
    $eventOfTypeA = Event::factory()->create(['type' => 'concert', 'location_id' => $locationA->id]);
    $eventOfTypeB = Event::factory()->create(['type' => 'stand-up', 'location_id' => $locationB->id]);

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $eventOfTypeA->locations()->attach($locationA->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $eventOfTypeB->locations()->attach($locationB->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $responseType = $this->get('filter?type=concert&city=')->assertStatus(200);

    $responseType->assertSee($eventOfTypeA->type);
    $responseType->assertDontSee($eventOfTypeB->type);

    $responseCity = $this->get('filter?type=&city=Paris')->assertStatus(200);

    $responseCity->assertSee($locationA->city);
    $responseCity->assertDontSee($locationB->city);

    $responseAll = $this->get('filter?type=concert&city=Paris')->assertStatus(200);

    $responseAll->assertSee($locationA->city);
    $responseType->assertSee($eventOfTypeA->type);
    $responseAll->assertDontSee($locationB->city);
    $responseType->assertDontSee($eventOfTypeB->type);
});
