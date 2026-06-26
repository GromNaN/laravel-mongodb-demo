<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        Group::truncate();

        $groups = [
            [
                'title' => 'Administrators',
                'user_title' => 'Administrator',
                'moderator' => true,
                'mod_edit_users' => true,
                'mod_rename_users' => true,
                'mod_change_passwords' => true,
                'mod_ban_users' => true,
                'mod_promote_users' => true,
                'post_replies' => true,
                'post_topics' => true,
                'edit_posts' => true,
                'delete_posts' => true,
                'delete_topics' => true,
                'set_title' => true,
                'search' => true,
                'search_users' => true,
                'send_email' => true,
                'post_flood' => 0,
                'search_flood' => 0,
                'email_flood' => 0,
                'report_flood' => 0,
                'promote_min_posts' => 0,
                'promote_next_group' => 0,
            ],
            [
                'title' => 'Moderators',
                'user_title' => 'Moderator',
                'moderator' => true,
                'mod_edit_users' => true,
                'mod_rename_users' => false,
                'mod_change_passwords' => false,
                'mod_ban_users' => true,
                'mod_promote_users' => false,
                'post_replies' => true,
                'post_topics' => true,
                'edit_posts' => true,
                'delete_posts' => true,
                'delete_topics' => true,
                'set_title' => true,
                'search' => true,
                'search_users' => true,
                'send_email' => true,
                'post_flood' => 0,
                'search_flood' => 0,
                'email_flood' => 60,
                'report_flood' => 0,
                'promote_min_posts' => 0,
                'promote_next_group' => 0,
            ],
            [
                'title' => 'Guests',
                'user_title' => 'Guest',
                'moderator' => false,
                'mod_edit_users' => false,
                'mod_rename_users' => false,
                'mod_change_passwords' => false,
                'mod_ban_users' => false,
                'mod_promote_users' => false,
                'post_replies' => false,
                'post_topics' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                'delete_topics' => false,
                'set_title' => false,
                'search' => true,
                'search_users' => true,
                'send_email' => false,
                'post_flood' => 0,
                'search_flood' => 60,
                'email_flood' => 0,
                'report_flood' => 0,
                'promote_min_posts' => 0,
                'promote_next_group' => 0,
            ],
            [
                'title' => 'Members',
                'user_title' => 'Member',
                'moderator' => false,
                'mod_edit_users' => false,
                'mod_rename_users' => false,
                'mod_change_passwords' => false,
                'mod_ban_users' => false,
                'mod_promote_users' => false,
                'post_replies' => true,
                'post_topics' => true,
                'edit_posts' => true,
                'delete_posts' => false,
                'delete_topics' => false,
                'set_title' => false,
                'search' => true,
                'search_users' => true,
                'send_email' => true,
                'post_flood' => 60,
                'search_flood' => 30,
                'email_flood' => 60,
                'report_flood' => 60,
                'promote_min_posts' => 0,
                'promote_next_group' => 0,
            ],
        ];

        foreach ($groups as $group) {
            Group::create($group);
        }
    }
}
