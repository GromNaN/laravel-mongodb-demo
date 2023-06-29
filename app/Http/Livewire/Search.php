<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;

class Search extends Component
{
    public $term = '';

    public function render()
    {
        $posts = Post::search($this->term)->paginate(5);

        $data = [
            'posts' => $posts,
        ];

        return view('livewire.search', $data);
    }
}
