<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Group extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'groups';

    const ADMIN = 1;
    const MODERATOR = 2;
    const GUEST = 3;
    const MEMBER = 4;

    protected $fillable = [
        'title',
        'user_title',
        'moderator',
        'mod_edit_users',
        'mod_rename_users',
        'mod_change_passwords',
        'mod_ban_users',
        'mod_promote_users',
        'post_replies',
        'post_topics',
        'edit_posts',
        'delete_posts',
        'delete_topics',
        'set_title',
        'search',
        'search_users',
        'send_email',
        'post_flood',
        'search_flood',
        'email_flood',
        'report_flood',
        'promote_min_posts',
        'promote_next_group',
    ];

    protected $casts = [
        'moderator'            => 'bool',
        'mod_edit_users'       => 'bool',
        'mod_rename_users'     => 'bool',
        'mod_change_passwords' => 'bool',
        'mod_ban_users'        => 'bool',
        'mod_promote_users'    => 'bool',
        'post_replies'         => 'bool',
        'post_topics'          => 'bool',
        'edit_posts'           => 'bool',
        'delete_posts'         => 'bool',
        'delete_topics'        => 'bool',
        'set_title'            => 'bool',
        'search'               => 'bool',
        'search_users'         => 'bool',
        'send_email'           => 'bool',
        'post_flood'           => 'int',
        'search_flood'         => 'int',
        'email_flood'          => 'int',
        'report_flood'         => 'int',
        'promote_min_posts'    => 'int',
        'promote_next_group'   => 'int',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'group_id');
    }
}
