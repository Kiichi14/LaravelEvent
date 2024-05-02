<?php

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use App\Models\Comment;

use function Pest\Laravel\withoutExceptionHandling;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can comment an event', function() {

    withoutExceptionHandling();

    $user = User::factory()->create();
    $location = Location::factory()->create();
    $event = Event::factory()->create();
    $comment =  Comment::factory()->raw(['user_id' => $user->id, 'event_id' => $event->id]);

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $event->locations()->attach($location->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $response = $this->actingAs($user)->post("event/{$event->id}/comment/", $comment);

    $response->assertStatus(201);

    $this->assertDatabaseHas('comments', [
        'comment' => $comment['comment'],
        'rating' => $comment['rating'],
    ]);

});

test('a guest cannot comment an event', function() {
    $location = Location::factory()->create();
    $event = Event::factory()->create();
    $comment =  Comment::factory()->raw();

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $event->locations()->attach($location->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $response = $this->post("event/{$event->id}/comment/", $comment);

    $response->assertStatus(302);
    $response->assertRedirect('/login');
});
