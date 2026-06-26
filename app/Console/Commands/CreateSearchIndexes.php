<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateSearchIndexes extends Command
{
    protected $signature = 'search:create-indexes';

    protected $description = 'Create MongoDB Atlas Search indexes for topics and posts collections';

    public function handle(): int
    {
        $indexes = [
            'topics' => [
                'mappings' => [
                    'dynamic' => false,
                    'fields' => [
                        'subject' => [['type' => 'string'], ['type' => 'token']],
                        'poster' => [['type' => 'token']],
                    ],
                ],
            ],
            'posts' => [
                'mappings' => [
                    'dynamic' => false,
                    'fields' => [
                        'message' => [['type' => 'string']],
                        'poster' => [['type' => 'token']],
                        'forum_id' => [['type' => 'token']],
                    ],
                ],
            ],
        ];

        foreach ($indexes as $collection => $definition) {
            try {
                DB::connection('mongodb')
                    ->getMongoDB()
                    ->selectCollection($collection)
                    ->createSearchIndex($definition, ['name' => 'default']);

                $this->info("Search index created on '{$collection}'.");
            } catch (\Exception $e) {
                $this->warn("Could not create index on '{$collection}': ".$e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
