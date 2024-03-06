<?php

namespace App\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Passenger;
use App\Models\SpaceShip;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('space_ships');
        $schema->create('space_ships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Passenger::truncate();
        SpaceShip::truncate();

        $spaceship = new SpaceShip();
        $spaceship->id = 1234;
        $spaceship->name = 'Nostromo';
        $spaceship->save();

        $passengerEllen = new Passenger();
        $passengerEllen->name = 'Ellen Ripley';

        $passengerDwayne = new Passenger();
        $passengerDwayne->name = 'Dwayne Hicks';

        $spaceship->passengers()->save($passengerEllen);
        $spaceship->passengers()->save($passengerDwayne);

        return 'ok';
    }
}
