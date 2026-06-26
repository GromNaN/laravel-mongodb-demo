<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::truncate();
        Forum::truncate();

        $general = Category::create([
            'name' => 'General',
            'position' => 1,
        ]);

        Forum::create([
            'name' => 'General Discussion',
            'description' => 'Talk about anything and everything here.',
            'category_id' => $general->_id,
            'position' => 1,
            'num_topics' => 0,
            'num_posts' => 0,
            'sort_by' => 0,
            'moderators' => [],
            'permissions' => [],
        ]);

        Forum::create([
            'name' => 'Suggestions',
            'description' => 'Have an idea to improve the forum? Post it here.',
            'category_id' => $general->_id,
            'position' => 2,
            'num_topics' => 0,
            'num_posts' => 0,
            'sort_by' => 0,
            'moderators' => [],
            'permissions' => [],
        ]);

        $support = Category::create([
            'name' => 'Support',
            'position' => 2,
        ]);

        Forum::create([
            'name' => 'Help & Support',
            'description' => 'Get help with any issues you may have.',
            'category_id' => $support->_id,
            'position' => 1,
            'num_topics' => 0,
            'num_posts' => 0,
            'sort_by' => 0,
            'moderators' => [],
            'permissions' => [],
        ]);
    }
}
