<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private array $users = [];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->loadUsers();
        $this->loadPosts();
    }

    private function loadUsers(): void
    {
        foreach ($this->getUserData() as [$name, $username, $password, $email]) {
            $this->users[$username] = $user = User::factory()->create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user->save();
        }
    }

    private function loadPosts(): void
    {
        foreach ($this->getPostData() as [$title, $summary, $content, $publishedAt, $author, $tags]) {
            $post = Post::factory()->create([
                'title' => $title,
                'summary' => $summary,
                'content' => $content,
                'published_at' => $publishedAt,
                'author_id' => $author->id,
            ]);
            $post->tags()->saveMany($tags);
            $post->save();

            foreach (range(1, 5) as $i) {
                $commentAuthor = $i % 2 ? $this->users['john_user'] : $author;

                Comment::factory()->create([
                    'content' => fake()->paragraph(2, true),
                    'post_id' => $post->id,
                    'author_id' => $commentAuthor->id,
                    'published_at' => (clone $post->published_at)->modify(sprintf('+%s minutes', $i * 3)),
                ]);
            }
        }
    }

    /**
     * @return array<array{string, string, string, string}>
     */
    private function getUserData(): array
    {
        return [
            // $userData = [$name, $username, $password, $email];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@laravel.com'],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@laravel.com'],
            ['John Doe', 'john_user', 'kitten', 'john_user@laravel.com'],
        ];
    }

    /**
     * @return string[]
     */
    private function getTagData(): array
    {
        return [
            // $name
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: \DateTime, 5: User, 6: array<Tag>}>
     *
     * @throws \Exception
     */
    private function getPostData(): array
    {
        $tags = array_flip($this->getTagData());
        $posts = [];
        foreach (fake()->sentences(30) as $i => $title) {
            // $postData = [$title, $slug, $summary, $content, $publishedAt, $author, $tags, $comments];

            /** @var User $user */
            $user = $this->users[['jane_admin', 'tom_admin'][0 === $i ? 0 : random_int(0, 1)]];

            $posts[] = [
                $title,
                fake()->paragraph(),
                fake()->paragraphs(5, true),
                fake()->dateTimeBetween('-1 year', 'now'),
                // Ensure that the first post is written by Jane Doe to simplify tests
                $user,
                // Take 2 or 3 random tags
                collect(array_rand($tags, random_int(2, 3)))
                    ->map(fn (string $name) => new Tag(['name' => $name]))
            ];
        }

        return $posts;
    }

    /**
     * @return string[]
     */
    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }
}
