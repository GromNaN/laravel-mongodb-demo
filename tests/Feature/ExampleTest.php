<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('list all posts', function () {
    $crawler = $this->client->request('GET', '/blog');


    $response = $this->get('/blog');

    $response->assertStatus(200);

    dd($response);
});
