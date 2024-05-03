<?php

use Database\Factories\CommentFactory;
use Database\Factories\EventFactory;
use Database\Factories\LocationFactory;
use App\Models\Comment;

use Database\Factories\UserFactory;
use function Pest\Laravel\withoutExceptionHandling;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

beforeEach(function () {
    $this->userFactory = new UserFactory();
    $this->locationFactory = new LocationFactory();
    $this->eventFactory = new EventFactory();
    $this->commentFactory = new CommentFactory();
});

test('a user can comment an event', function() {

    $user = $this->userFactory->create();
    $location = $this->locationFactory->create();
    $event = $this->eventFactory->create();
    $comment = $this->commentFactory->raw(['user_id' => $user->id, 'event_id' => $event->id]);

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

    $location = $this->locationFactory->create();
    $event = $this->eventFactory->create();
    $comment = $this->commentFactory->raw(['event_id' => $event->id]);

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

test('should get the comment with an event', function() {

    withoutExceptionHandling();

    $user = $this->userFactory->create();
    $location = $this->locationFactory->create();
    $event = $this->eventFactory->create();
    $comment = $this->commentFactory->create(['user_id' => $user->id, 'event_id' => $event->id]);

    $fakeTheater = "Fake theater";
    $fakePlace = 240;
    $fakeDate = "2024-10-22";

    $event->locations()->attach($location->id, attributes: [
        'theater' => $fakeTheater,
        'place_number' => $fakePlace,
        'date' => $fakeDate
    ]);

    $response = $this->get('event/'.$event->id)->assertStatus(200);

    $this->assertTrue($event->comments->count() > 0);

    $this->assertInstanceOf(Comment::class, $event->comments()->first());

});
