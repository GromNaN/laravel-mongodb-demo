<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::where('username', 'admin')->delete();

        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
            'group_id' => 1,
            'title' => 'Administrator',
            'num_posts' => 0,
            'registered' => now(),
            'registration_ip' => '127.0.0.1',
            'last_visit' => now(),
            'is_banned' => false,
            'email_setting' => 1,
            'preferences' => [
                'language' => 'English',
                'style' => 'default',
                'timezone' => 0,
                'dst' => 0,
                'time_format' => 'H:i:s',
                'date_format' => 'Y-m-d',
                'view_avatars' => 1,
                'show_sig' => 1,
                'show_smilies' => 1,
                'show_img' => 1,
                'show_img_sig' => 1,
                'show_avatars' => 1,
                'show_email' => 0,
                'notify_on_post' => 0,
            ],
        ]);
    }
}
