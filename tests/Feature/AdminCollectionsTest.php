<?php

use App\Models\CollectionItem;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Use the Bagisto admin test case so authentication + admin layout work.
uses(Webkul\Admin\Tests\AdminTestCase::class);

beforeEach(function () {
    $this->withoutExceptionHandling();
});

test('admin collection index pages render for every type', function (string $type) {
    $this->loginAsAdmin();

    $this->get(route('admin.'.$type.'.index'))
        ->assertOk()
        ->assertSee('Create');
})->with(['ideas', 'places', 'culture', 'create']);

test('admin games index page renders', function () {
    $this->loginAsAdmin();

    $this->get(route('admin.games.index'))
        ->assertOk()
        ->assertSee('Create Game');
});

test('admin create pages render for every collection type', function (string $type) {
    $this->loginAsAdmin();

    $this->get(route('admin.'.$type.'.create'))
        ->assertOk()
        ->assertSee('Name');
})->with(['ideas', 'places', 'culture', 'create']);

test('admin games create page renders', function () {
    $this->loginAsAdmin();

    $this->get(route('admin.games.create'))
        ->assertOk()
        ->assertSee('Title');
});

test('admin can store a new idea item', function () {
    $this->loginAsAdmin();

    $this->post(route('admin.ideas.store'), [
        'name' => 'Test Philosophy',
        'category' => 'Vedic',
        'overview' => "First paragraph\nSecond paragraph",
        'core_ideas' => "Niyati|An iron law of destiny\nUniversal Liberation",
    ])->assertRedirect(route('admin.ideas.index'));

    $item = CollectionItem::ofType('ideas')->where('name', 'Test Philosophy')->first();
    expect($item)->not->toBeNull()
        ->and($item->slug)->toBe('test-philosophy')
        ->and($item->category)->toBe('Vedic')
        ->and($item->overview)->toBe(['First paragraph', 'Second paragraph'])
        ->and($item->core_ideas)->toEqual([
            ['title' => 'Niyati', 'text' => 'An iron law of destiny'],
            ['title' => '', 'text' => 'Universal Liberation'],
        ]);
});

test('admin can store a new place with coordinates', function () {
    $this->loginAsAdmin();

    $this->post(route('admin.places.store'), [
        'name' => 'Test Fort',
        'latitude' => '27.1750075',
        'longitude' => '78.0421013',
    ])->assertRedirect(route('admin.places.index'));

    $item = CollectionItem::ofType('places')->where('name', 'Test Fort')->first();
    expect($item)->not->toBeNull()
        ->and($item->latitude)->toBeFloat()->toBe(27.1750075)
        ->and($item->longitude)->toBeFloat()->toBe(78.0421013);
});

test('admin can store a new game with tags', function () {
    $this->loginAsAdmin();

    $this->post(route('admin.games.store'), [
        'title' => 'Test Game',
        'tagline' => 'A test game',
        'path' => 'play/test-game',
        'tags' => "Dices|2–4 players\nStrategy",
    ])->assertRedirect(route('admin.games.index'));

    $game = Game::where('title', 'Test Game')->first();
    expect($game)->not->toBeNull()
        ->and($game->path)->toBe('/play/test-game')
        ->and($game->tags)->toBe([
            ['icon' => 'Dices', 'label' => '2–4 players'],
            ['label' => 'Strategy'],
        ]);
});

test('admin edit pages render for every collection type', function (string $type) {
    $this->loginAsAdmin();

    $item = CollectionItem::ofType($type)->firstOrFail();

    $this->get(route('admin.'.$type.'.edit', $item->id))
        ->assertOk()
        ->assertSee($item->name);
})->with(['ideas', 'places', 'culture', 'create']);

test('admin can update an idea item', function () {
    $this->loginAsAdmin();

    $item = CollectionItem::ofType('ideas')->create([
        'type' => 'ideas',
        'slug' => 'update-me',
        'name' => 'Update Me',
    ]);

    $this->put(route('admin.ideas.update', $item->id), [
        'name' => 'Updated Name',
        'category' => 'Devotional',
        'overview' => "New paragraph",
        'core_ideas' => "Ideas|Updated core idea",
    ])->assertRedirect(route('admin.ideas.index'));

    $item->refresh();
    expect($item->name)->toBe('Updated Name')
        ->and($item->category)->toBe('Devotional')
        ->and($item->overview)->toBe(['New paragraph'])
        ->and($item->core_ideas)->toEqual([['title' => 'Ideas', 'text' => 'Updated core idea']]);

    $item->delete();
});

test('admin can destroy an idea item', function () {
    $this->loginAsAdmin();

    $item = CollectionItem::ofType('ideas')->create([
        'type' => 'ideas',
        'slug' => 'delete-me',
        'name' => 'Delete Me',
    ]);

    $this->delete(route('admin.ideas.destroy', $item->id))
        ->assertRedirect(route('admin.ideas.index'));

    expect(CollectionItem::ofType('ideas')->where('id', $item->id)->exists())->toBeFalse();
});

test('admin can update a game', function () {
    $this->loginAsAdmin();

    $game = Game::create([
        'title' => 'Editable Game',
        'path' => '/play/editable',
    ]);

    $this->put(route('admin.games.update', $game->id), [
        'title' => 'Edited Game',
        'tagline' => 'Updated tagline',
        'path' => 'play/edited',
        'tags' => "Users|2 players",
    ])->assertRedirect(route('admin.games.index'));

    $game->refresh();
    expect($game->title)->toBe('Edited Game')
        ->and($game->path)->toBe('/play/edited')
        ->and($game->tags)->toBe([['icon' => 'Users', 'label' => '2 players']]);

    $game->delete();
});
