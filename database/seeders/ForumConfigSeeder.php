<?php

namespace Database\Seeders;

use App\Models\ForumConfig;
use Illuminate\Database\Seeder;

class ForumConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            'board_title' => 'My FluxBB Forum',
            'board_desc' => 'A simple forum powered by FluxBB for Laravel',
            'admin_email' => 'admin@example.com',
            'base_url' => 'http://localhost:8000',
            'language' => 'English',
            'default_style' => 'default',
            'time_format' => 'H:i:s',
            'date_format' => 'Y-m-d',
            'timezone' => '0',
            'dst' => '0',
            'default_lang' => 'English',
            'topics_per_page' => '25',
            'posts_per_page' => '25',
            'users_per_page' => '50',
            'preview_enabled' => '1',
            'registration_enabled' => '1',
            'registration_email_verification' => '0',
            'posting_enabled' => '1',
            'search_enabled' => '1',
            'allow_dupe_email' => '0',
            'feed_type' => '1',
            'feed_ttl' => '0',
            'report_flood' => '60',
            'max_post_size' => '65535',
            'announcement' => '',
            'announcement_message' => '',
            'rules' => '0',
            'rules_message' => '',
            'smilies' => '1',
            'smilies_sig' => '1',
            'sig_length' => '400',
            'sig_lines' => '4',
            'avatars' => '1',
            'avatars_dir' => 'img/avatars',
            'avatars_width' => '60',
            'avatars_height' => '60',
            'avatars_size' => '10240',
            'email_transport' => 'sendmail',
            'smtp_host' => '',
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_ssl' => '0',
            'o_maintenance' => '0',
            'o_maintenance_message' => 'The board is temporarily down for maintenance. Please try again later.',
        ];

        foreach ($configs as $key => $value) {
            ForumConfig::set($key, $value);
        }
    }
}
