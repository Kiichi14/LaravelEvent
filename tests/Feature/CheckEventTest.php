<?php

use App\Models\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('should return a simple list of all event', function() {
    $this->assertTrue(Schema::hasTable('events'));

    $this->assertTrue(Schema::hasColumns('events', [
        'id', 'name', 'description', 'type'
    ]));

    $events = Event::factory()->count(5)->create();

    $rows = Event::all();

    $response = $this->get('/list');

    $response->assertStatus(200);

    $response->assertJsonCount($rows->count());

});
