<?php

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use App\Models\Comment;

use function Pest\Laravel\withoutExceptionHandling;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can comment an event', function() {
    $user = User::factory()->create();
    $location = Location::factory()->create();
    $event = Event::factory()->create();
    $comment =  Comment::factory()->create();

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $event->locations()->attach($location->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $response = $this->actingAs($user)->post(`event/{$event->id}/comment/`, $comment);

    $response->assertStatus(201);

});

test('a guest connot comment an event', function() {
    $location = Location::factory()->create();
    $event = Event::factory()->create();
    $comment =  Comment::factory()->create();

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $event->locations()->attach($location->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $response = $this->post(`event/{$event->id}/comment/`, $comment);

    $response->assertStatus(401);
});
