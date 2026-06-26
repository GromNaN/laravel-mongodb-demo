<?php

namespace Database\Seeders;

use App\Models\ForumConfig;
use Illuminate\Database\Seeder;

class ForumConfigSeeder extends Seeder
{
    public function run(): void
    {
        ForumConfig::truncate();

        ForumConfig::create([
            'board_title'                     => 'My FluxBB Forum',
            'board_desc'                      => 'A simple forum powered by FluxBB for Laravel',
            'admin_email'                     => 'admin@example.com',
            'base_url'                        => 'http://localhost:8000',
            'language'                        => 'English',
            'default_style'                   => 'default',
            'time_format'                     => 'H:i:s',
            'date_format'                     => 'Y-m-d',
            'timezone'                        => '0',
            'dst'                             => false,
            'default_lang'                    => 'English',
            'topics_per_page'                 => 25,
            'posts_per_page'                  => 25,
            'users_per_page'                  => 50,
            'preview_enabled'                 => true,
            'registration_enabled'            => true,
            'registration_email_verification' => false,
            'posting_enabled'                 => true,
            'search_enabled'                  => true,
            'allow_dupe_email'                => false,
            'feed_type'                       => 1,
            'feed_ttl'                        => 0,
            'report_flood'                    => 60,
            'max_post_size'                   => 65535,
            'announcement'                    => false,
            'announcement_message'            => '',
            'rules'                           => false,
            'rules_message'                   => '',
            'smilies'                         => true,
            'smilies_sig'                     => true,
            'sig_length'                      => 400,
            'sig_lines'                       => 4,
            'avatars'                         => true,
            'avatars_dir'                     => 'img/avatars',
            'avatars_width'                   => 60,
            'avatars_height'                  => 60,
            'avatars_size'                    => 10240,
            'email_transport'                 => 'sendmail',
            'smtp_host'                       => '',
            'smtp_user'                       => '',
            'smtp_pass'                       => '',
            'smtp_ssl'                        => false,
            'o_maintenance'                   => false,
            'o_maintenance_message'           => 'The board is temporarily down for maintenance. Please try again later.',
            'censor_rules'                    => [],
        ]);
    }
}
