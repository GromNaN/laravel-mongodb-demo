<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_movies_search_return_filtered_list(): void
    {
        $response = $this->get('/movies');

        $response->assertStatus(200);
        $response->assertSee('Next &raquo;');

        $response = $this->get('/movies?search=Pelham+One');

        $response->assertStatus(200);
        $response->assertSee('The Taking of Pelham One Two Three');
        $response->assertSee('The Taking of Pelham 1 2 3');
    }


    public function test_comments_are_part_of_the_search_data()
    {
        DB::connection('mongodb')->enableQueryLog();

        $movie = Movie::where('title', '=', 'The Taking of Pelham 1 2 3')->first();

        $this->assertGreaterThan(10, $movie->comments->count());

        $searchable = $movie->toSearchableArray();

        $this->assertIsArray($searchable);
    }
}
