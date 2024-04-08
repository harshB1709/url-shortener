<?php

use App\Models\User;
use App\Models\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows authenticated users to create a short url', function () {
    $user = User::factory()->create();
    $data = ['original_url' => 'https://example.com'];

    $response = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(route('url.store'), $data);

    $response->assertStatus(200);
    $this->assertDatabaseHas('urls', ['original_url' => 'https://example.com']);
});

it('prevents unauthenticated users from creating a short url', function () {
    $data = ['original_url' => 'https://example.com'];

    $response = $this->post(route('url.store'), $data);

    $response->assertRedirect('/login');
});

it('redirects to the original url', function () {
    $user = User::factory()->create();
    $shortUrl = Url::create([
        'user_id' => $user->id,
        'original_url' => 'https://example.com',
        'short_code' => 'abc123',
    ]);

    $response = $this->get(route('url.access', $shortUrl->short_code));

    $response->assertRedirect('https://example.com');
});

it('returns a validation error if original url is missing', function () {
    $user = User::factory()->create();
    $data = [];

    $response = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(route('url.store'), $data);

    $response->assertStatus(422);
});

it('generates a unique short code for each url', function () {
    $user = User::factory()->create();

    $data1 = ['original_url' => 'https://example.org'];
    $url1 = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(route('url.store'), $data1)->json('newUrl');

    $data2 = ['original_url' => 'https://example.org'];
    $url2 = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(route('url.store'), $data2)->json('newUrl');

    expect($url1['short_code'])->not->toEqual($url2['short_code']);
});

it('does not allow users to create more than the basic limit of shortened urls', function() {
    $user = User::factory()->create();
    $basic_limit = config('app.basic_plan_limit', 10);
    $urls_data = Arr::map(range(1, $basic_limit), fn($value, $key) => ['original_url' => "https://example{$value}.org", 'short_code' => $value . ((string) Str::random(7))]);
    
    $user->urls()->createMany($urls_data);

    expect($user->urls->count())->toEqual($basic_limit);

    $new_url = ['original_url' => 'https://example3.org'];

    $response = $this->actingAs($user)
                ->withHeaders(['Accept' => 'application/json'])
                ->post(route('url.store'), $new_url);

    $response->assertStatus(403); 
});