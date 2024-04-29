<?php

use App\Models\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('should return a simple list of all event', function() {

    $eventsCount = 5;

    $events = Event::factory()->count($eventsCount)->create();

    $response = $this->get('/list')->assertStatus(200);

    $response->assertJsonCount($events->count($eventsCount));

});


