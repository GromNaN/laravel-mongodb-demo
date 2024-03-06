<?php

namespace Tests;

use App\Models\Post;
use App\Models\Tag;

class TestTest extends TestCase
{
    public function testX()
    {
        $account = Post::find('65cb6c11445994116d082b25');
        $account->tag = new Tag();
        $account->save();

        $account = Post::find('65cb6c11445994116d082b25');
        $tag = $account->tag;
        $tag->names = ['some data'];
        $tag->save();
    }

    public function testDump()
    {
        Post::where('foo', 'bar')->dump();
    }
}
