<?php


use App\Models\User;

beforeEach(function () {
    $user = User::where('name', 'Jane Doe')->first();
    $this->actingAs($user, 'web');
});

it('admin login successful', function () {
    $response = $this->get('/admin/post');
    $response->assertStatus(200);
});

it('list posts', function () {
    $response = $this->get('/admin/post');
    $response->assertStatus(200);
});
