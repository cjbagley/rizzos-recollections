<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\Game;
use App\Models\GameCapture;
use App\Models\Scopes\SensitiveGameCaptureScope;
use App\Models\Scopes\SensitiveGameScope;
use App\Models\Scopes\SensitiveTagScope;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

uses(
    Tests\TestCase::class,
    Tests\CreatesApplication::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

const SENSITIVE_GAME_TITLE = 'Sensitive Game';
const SENSITIVE_CAPTURE_TITLE = 'Sensitive Cap';
const SENSITIVE_TAG_TITLE = 'Sensitive Tag';

function adminUserTest(Closure $test)
{
    return describe('Admin User', $test);
}

function guestUserTest(Closure $test)
{
    return describe('Guest User', $test);
}

function loggedUserTest(Closure $test)
{
    return describe('Logged User', $test);
}

function create_test_user(array $attrs = []): User
{
    return User::factory()->create($attrs);
}

function create_admin_user(array $attrs = []): User
{
    $attrs = array_merge($attrs, ['admin_token' => Hash::make(config('auth.admin_token'))]);

    return User::factory()->create($attrs);
}

function asLoggedUser(): TestCase
{
    return test()->actingAs(create_test_user());
}

function asAdmin(): TestCase
{
    return test()->actingAs(create_admin_user());
}

function create_test_game(array $attrs = []): Game
{
    return Game::factory()->create($attrs);
}

function create_test_tag(array $attrs = []): Tag
{
    return Tag::factory()->create($attrs);
}

function create_dummy_games_and_captures()
{
    // Create mix of sensitive and non-sensitive tags
    Tag::factory()->count(5)->create(['is_sensitive' => false]);
    $sensitive_tag = Tag::factory()->create(['is_sensitive' => true, 'tag' => SENSITIVE_TAG_TITLE]);

    // Create mix of sensitive and non-sensitive games
    Game::factory()->count(5)->create(['is_sensitive' => false]);
    Game::factory()->create(['is_sensitive' => true, 'title' => SENSITIVE_GAME_TITLE]);

    $games = Game::withoutGlobalScope(SensitiveGameScope::class)->get();
    if ($games->count() != 6) {
        dd(sprintf('%s should create 6 games, %s given', __METHOD__, $games->count()));
    }

    // Make sure a non-sensitive game has a capture with a sensitive tag
    $non_sensitive_game = Game::where('is_sensitive', false)->first();
    $sensitive_capture = GameCapture::factory()->create(['game_id' => $non_sensitive_game->id, 'title' => SENSITIVE_CAPTURE_TITLE]);
    $sensitive_capture->tags()->attach([$sensitive_tag->id]);

    // Create captures against games
    foreach ($games as $game) {
        GameCapture::factory()->count(5)->create(['game_id' => $game->id]);
    }

    // Set tags against the captures
    $tags = Tag::withoutGlobalScope(SensitiveTagScope::class)->get();
    foreach (GameCapture::withoutGlobalScope(SensitiveGameCaptureScope::class)->get() as $gameCapture) {
        $selected_tags = $tags->random(2)->pluck('id')->toArray();
        $gameCapture->tags()->attach($selected_tags);
    }
}

function getListPageData(TestResponse $response): array
{
    $page = $response->viewData('page');
    if (! isset($page['props']['data']['data']['data'])) {
        throw new Exception('Games list missing');
    }

    return $page['props']['data']['data']['data'];
}

function getGamesPageData(TestResponse $response): array
{
    $page = $response->viewData('page');
    if (! isset($page['props']['games'])) {
        throw new Exception('Games missing');
    }

    return $page['props']['games'];
}
