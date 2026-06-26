<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ForumConfig extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'config';

    protected $fillable = [
        'board_title',
        'board_desc',
        'admin_email',
        'base_url',
        'language',
        'default_style',
        'time_format',
        'date_format',
        'timezone',
        'dst',
        'default_lang',
        'topics_per_page',
        'posts_per_page',
        'users_per_page',
        'preview_enabled',
        'registration_enabled',
        'registration_email_verification',
        'posting_enabled',
        'search_enabled',
        'allow_dupe_email',
        'feed_type',
        'feed_ttl',
        'report_flood',
        'max_post_size',
        'announcement',
        'announcement_message',
        'rules',
        'rules_message',
        'smilies',
        'smilies_sig',
        'sig_length',
        'sig_lines',
        'avatars',
        'avatars_dir',
        'avatars_width',
        'avatars_height',
        'avatars_size',
        'email_transport',
        'smtp_host',
        'smtp_user',
        'smtp_pass',
        'smtp_ssl',
        'o_maintenance',
        'o_maintenance_message',
        'censor_rules',
    ];

    protected $casts = [
        'topics_per_page'                  => 'int',
        'posts_per_page'                   => 'int',
        'users_per_page'                   => 'int',
        'max_post_size'                    => 'int',
        'report_flood'                     => 'int',
        'sig_length'                       => 'int',
        'sig_lines'                        => 'int',
        'avatars_width'                    => 'int',
        'avatars_height'                   => 'int',
        'avatars_size'                     => 'int',
        'feed_ttl'                         => 'int',
        'preview_enabled'                  => 'bool',
        'registration_enabled'             => 'bool',
        'registration_email_verification'  => 'bool',
        'posting_enabled'                  => 'bool',
        'search_enabled'                   => 'bool',
        'allow_dupe_email'                 => 'bool',
        'smilies'                          => 'bool',
        'smilies_sig'                      => 'bool',
        'avatars'                          => 'bool',
        'announcement'                     => 'bool',
        'rules'                            => 'bool',
        'smtp_ssl'                         => 'bool',
        'o_maintenance'                    => 'bool',
        'dst'                              => 'bool',
        'censor_rules'                     => 'array',
    ];

    /** Return the singleton config document, creating it if absent. */
    public static function instance(): static
    {
        return static::first() ?? static::create([]);
    }
}
