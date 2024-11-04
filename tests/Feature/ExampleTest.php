<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    #[Test] public function it_reads_a_movie_with_at_least_two_comments()
    {
        DB::connection('mongodb')->enableQueryLog();
        // Retrieve the movie with comments
        $movie = Movie::with('comments')->find('573a13bff29313caabd5e91e');

        // Assert that the movie has at least two comments
        $this->assertCount(161, $movie->comments);
    }
}
