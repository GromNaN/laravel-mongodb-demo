<?php

namespace Database\Seeders;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Clean up any previous test data (idempotent re-runs)
        // ----------------------------------------------------------------
        $testUsernames = ['alice', 'bob', 'charlie', 'diana', 'eve'];
        User::whereIn('username', $testUsernames)->delete();

        // ----------------------------------------------------------------
        // 2. Create test users
        // ----------------------------------------------------------------
        $defaultPreferences = [
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
        ];

        $alice = User::create([
            'username' => 'alice',
            'email' => 'alice@example.com',
            'password' => Hash::make('password123'),
            'group_id' => 4,
            'title' => 'Member',
            'num_posts' => 0,
            'registered' => Carbon::now()->subDays(25),
            'registration_ip' => '192.168.1.10',
            'last_visit' => Carbon::now()->subHours(2),
            'last_active' => Carbon::now()->subHours(2),
            'is_banned' => false,
            'email_setting' => 1,
            'preferences' => $defaultPreferences,
        ]);

        $bob = User::create([
            'username' => 'bob',
            'email' => 'bob@example.com',
            'password' => Hash::make('password123'),
            'group_id' => 4,
            'title' => 'Member',
            'num_posts' => 0,
            'registered' => Carbon::now()->subDays(22),
            'registration_ip' => '192.168.1.11',
            'last_visit' => Carbon::now()->subHours(5),
            'last_active' => Carbon::now()->subHours(5),
            'is_banned' => false,
            'email_setting' => 1,
            'preferences' => $defaultPreferences,
        ]);

        $charlie = User::create([
            'username' => 'charlie',
            'email' => 'charlie@example.com',
            'password' => Hash::make('password123'),
            'group_id' => 4,
            'title' => 'Member',
            'num_posts' => 0,
            'registered' => Carbon::now()->subDays(18),
            'registration_ip' => '192.168.1.12',
            'last_visit' => Carbon::now()->subDays(1),
            'last_active' => Carbon::now()->subDays(1),
            'is_banned' => false,
            'email_setting' => 1,
            'preferences' => $defaultPreferences,
        ]);

        $diana = User::create([
            'username' => 'diana',
            'email' => 'diana@example.com',
            'password' => Hash::make('password123'),
            'group_id' => 2,
            'title' => 'Moderator',
            'num_posts' => 0,
            'registered' => Carbon::now()->subDays(30),
            'registration_ip' => '192.168.1.13',
            'last_visit' => Carbon::now()->subMinutes(30),
            'last_active' => Carbon::now()->subMinutes(30),
            'is_banned' => false,
            'email_setting' => 1,
            'preferences' => $defaultPreferences,
        ]);

        $eve = User::create([
            'username' => 'eve',
            'email' => 'eve@example.com',
            'password' => Hash::make('password123'),
            'group_id' => 4,
            'title' => 'Member',
            'num_posts' => 0,
            'registered' => Carbon::now()->subDays(15),
            'registration_ip' => '192.168.1.14',
            'last_visit' => Carbon::now()->subDays(10),
            'last_active' => Carbon::now()->subDays(10),
            'is_banned' => true,
            'email_setting' => 1,
            'preferences' => $defaultPreferences,
        ]);

        // ----------------------------------------------------------------
        // 3. Load existing admin user and forums
        // ----------------------------------------------------------------
        $admin = User::where('username', 'admin')->firstOrFail();

        $forumGeneral = Forum::where('name', 'General Discussion')->firstOrFail();
        $forumSuggestion = Forum::where('name', 'Suggestions')->firstOrFail();
        $forumSupport = Forum::where('name', 'Help & Support')->firstOrFail();

        // Track all created topics/posts so we can update counts afterwards
        $allTopics = [];

        // ----------------------------------------------------------------
        // 4. Helper closure: create a topic with its first post
        // ----------------------------------------------------------------
        $createTopic = function (
            Forum $forum,
            string $subject,
            User $poster,
            string $firstMessage,
            Carbon $postedAt,
            bool $sticky = false,
            bool $closed = false
        ) use (&$allTopics): Topic {
            $topic = Topic::create([
                'forum_id' => (string) $forum->_id,
                'subject' => $subject,
                'poster' => $poster->username,
                'poster_id' => (string) $poster->_id,
                'posted' => $postedAt,
                'num_views' => random_int(15, 250),
                'num_replies' => 0,
                'sticky' => $sticky,
                'closed' => $closed,
            ]);

            $firstPost = Post::create([
                'topic_id' => (string) $topic->_id,
                'forum_id' => (string) $forum->_id,
                'poster' => $poster->username,
                'poster_id' => (string) $poster->_id,
                'poster_ip' => '127.0.0.1',
                'posted' => $postedAt,
                'message' => $firstMessage,
                'hide_smilies' => false,
            ]);

            $topic->first_post_id = (string) $firstPost->_id;
            $topic->save();

            $allTopics[] = $topic;

            return $topic;
        };

        // Helper closure: add a reply post to a topic
        $addReply = function (
            Topic $topic,
            Forum $forum,
            User $poster,
            string $message,
            Carbon $postedAt
        ): Post {
            return Post::create([
                'topic_id' => (string) $topic->_id,
                'forum_id' => (string) $forum->_id,
                'poster' => $poster->username,
                'poster_id' => (string) $poster->_id,
                'poster_ip' => '127.0.0.1',
                'posted' => $postedAt,
                'message' => $message,
                'hide_smilies' => false,
            ]);
        };

        // ----------------------------------------------------------------
        // 5. Topics in "General Discussion"
        // ----------------------------------------------------------------

        // Topic 1: Welcome thread — 6 replies
        $t1 = $createTopic(
            $forumGeneral,
            'Welcome to the forum! Introduction thread',
            $alice,
            "Hi everyone! I'm Alice and I'm really excited to be part of this community. "
            ."I've been looking for a place like this for a while — feel free to introduce yourself below. "
            ."Let's make this a welcoming space for everyone!",
            Carbon::now()->subDays(28)->subHours(3)
        );
        $addReply($t1, $forumGeneral, $bob,
            "Hey Alice, great idea for a thread! I'm Bob, a software developer from Portland. "
            .'I mostly hang out in the programming corners of the internet but this forum looks really promising. '
            .'Looking forward to good discussions here.',
            Carbon::now()->subDays(27)->subHours(10));
        $addReply($t1, $forumGeneral, $charlie,
            'Heya! Charlie here. I work in data engineering and love talking about open-source tools. '
            .'Excited to meet like-minded people. This place has a nice vibe already.',
            Carbon::now()->subDays(27)->subHours(6));
        $addReply($t1, $forumGeneral, $diana,
            "Welcome to all the newcomers! I'm Diana, one of the moderators here. "
            ."Don't hesitate to reach out if you have any questions about the forum or need help navigating things. "
            ."We're glad to have you all!",
            Carbon::now()->subDays(26)->subHours(14));
        $addReply($t1, $forumGeneral, $alice,
            'Thank you all for the warm welcome! Already feeling at home. '
            .'Bob, Portland is awesome — I visited a couple of years ago. Charlie, data engineering is fascinating!',
            Carbon::now()->subDays(25)->subHours(9));
        $addReply($t1, $forumGeneral, $alice,
            'Quick update: I added a short bio to my profile. '
            .'If anyone else wants to share a bit more about themselves feel free to do so, this thread is for everyone. '
            .'Loving the community so far!',
            Carbon::now()->subDays(24)->subHours(4));
        $addReply($t1, $forumGeneral, $admin,
            "Wonderful thread! It's great to see the community growing. "
            .'Keep the introductions coming — this is exactly what forums should feel like.',
            Carbon::now()->subDays(23)->subHours(11));

        // Topic 2: Favourite programming languages — 8 replies
        $t2 = $createTopic(
            $forumGeneral,
            'What are your favorite programming languages?',
            $bob,
            "I've been coding for about eight years now and my go-to language is Python — it's just so expressive. "
            ."Lately I've been experimenting with Rust for systems-level work and I'm genuinely impressed. "
            .'What languages do you all swear by?',
            Carbon::now()->subDays(20)->subHours(7)
        );
        $addReply($t2, $forumGeneral, $alice,
            'Python all the way for me too! The ecosystem is unbeatable for data work. '
            .'I also really enjoy TypeScript these days — type safety without the verbosity of Java is a dream. '
            .'Have you tried Go, Bob? It has a surprisingly clean concurrency model.',
            Carbon::now()->subDays(20)->subHours(5));
        $addReply($t2, $forumGeneral, $charlie,
            "I'll throw Scala into the mix — it's fantastic for distributed data pipelines with Spark. "
            ."Not the easiest language to learn but once it clicks it's incredibly powerful. "
            .'Also have a soft spot for Elixir for anything web-related.',
            Carbon::now()->subDays(19)->subHours(15));
        $addReply($t2, $forumGeneral, $diana,
            'From a moderation standpoint I mostly use Python scripts to automate forum tasks. '
            ."But professionally I'm a big PHP fan — yes, modern PHP is actually great! "
            .'Laravel in particular is a joy to work with.',
            Carbon::now()->subDays(19)->subHours(8));
        $addReply($t2, $forumGeneral, $bob,
            'Alice, yes! I dabbled with Go last year and the goroutine model is brilliant. '
            .'The compile times are so fast it almost feels scripted. '
            ."Diana, you're right that modern PHP has come a long way — I was too quick to dismiss it.",
            Carbon::now()->subDays(18)->subHours(12));
        $addReply($t2, $forumGeneral, $alice,
            'Agreed on Go. The standard library is surprisingly comprehensive too. '
            .'Charlie, I tried Elixir for a side project last year — the pattern matching syntax is gorgeous. '
            .'The Phoenix framework is incredibly fast.',
            Carbon::now()->subDays(17)->subHours(3));
        $addReply($t2, $forumGeneral, $admin,
            'Great thread! Seeing such diverse language preferences really shows the range of experience here. '
            .'I personally started with C and it still shapes how I think about memory and performance today. '
            ."Nothing beats understanding what's happening under the hood.",
            Carbon::now()->subDays(16)->subHours(9));
        $addReply($t2, $forumGeneral, $charlie,
            "That's a great point, admin. Low-level languages really do sharpen your thinking. "
            .'I spent a semester writing assembly in university and it was painful but educational. '
            .'Now I appreciate every abstraction layer so much more.',
            Carbon::now()->subDays(15)->subHours(7));
        $addReply($t2, $forumGeneral, $bob,
            'Assembly respect indeed! Every developer should try it at least once. '
            .'Anyway, seems like Python, Rust and Go are the crowd favourites here. '
            .'Maybe we should run a proper poll next time?',
            Carbon::now()->subDays(14)->subHours(2));

        // Topic 3: Forum rules — sticky + closed — 2 replies by admin
        $t3 = $createTopic(
            $forumGeneral,
            '[STICKY] Forum rules and guidelines',
            $admin,
            "Welcome to our community! Please take a moment to read these rules before posting.\n\n"
            ."1. Be respectful to all members — no personal attacks or harassment.\n"
            ."2. Keep posts on-topic and constructive.\n"
            ."3. No spam, self-promotion, or duplicate posts.\n"
            ."4. Use the search function before creating a new thread.\n"
            ."5. Report rule violations rather than engaging with them.\n\n"
            .'Failure to follow these rules may result in warnings or a ban. Thank you for keeping this a great place!',
            Carbon::now()->subDays(30)->subHours(1),
            sticky: true,
            closed: true
        );
        $addReply($t3, $forumGeneral, $admin,
            "Update (rule 6): Please use descriptive thread titles — titles like 'help' or 'question' will be removed. "
            .'A good title helps others find your thread in the future and is a courtesy to those who want to help you.',
            Carbon::now()->subDays(20)->subHours(2));
        $addReply($t3, $forumGeneral, $admin,
            'Update (rule 7): Off-topic replies in technical threads will be moved or deleted without notice. '
            .'We have a General Discussion forum for casual conversation — please use it appropriately. '
            .'Thanks everyone for your continued cooperation.',
            Carbon::now()->subDays(10)->subHours(1));

        // Topic 4: Coffee or tea — 4 replies
        $t4 = $createTopic(
            $forumGeneral,
            'Coffee or tea? The eternal debate',
            $charlie,
            "Alright, I'm settling this once and for all: are you a coffee person or a tea person? "
            ."I'm firmly in the coffee camp — there's nothing like a strong espresso to kick-start a morning coding session. "
            .'Change my mind.',
            Carbon::now()->subDays(12)->subHours(8)
        );
        $addReply($t4, $forumGeneral, $alice,
            'Tea, absolutely! Green tea in the morning, chamomile in the evening. '
            ."Coffee gives me the jitters and I end up writing code that's way too clever for its own good. "
            .'Readable code is happy code — and that means tea.',
            Carbon::now()->subDays(12)->subHours(6));
        $addReply($t4, $forumGeneral, $bob,
            "Neither — I'm a water-and-sleep maximalist. Just kidding, I'm deeply in the coffee cult. "
            .'Specifically pour-over with single-origin beans. Yes I am that person at the coffee shop. '
            .'No regrets.',
            Carbon::now()->subDays(11)->subHours(14));
        $addReply($t4, $forumGeneral, $diana,
            'I switch depending on the season. Iced coffee in summer, hot tea in winter. '
            .'The real answer is whichever one is already brewed when I sit down at my desk. '
            .'Pragmatism wins every time.',
            Carbon::now()->subDays(10)->subHours(9));
        $addReply($t4, $forumGeneral, $charlie,
            "Diana's pragmatism is undefeated. Alice, I'll agree that chamomile before bed is fantastic. "
            .'But I stand by my espresso for peak performance. '
            .'Maybe the real answer is: the right drink for the right task.',
            Carbon::now()->subDays(9)->subHours(5));

        // Topic 5: Best open source projects 2025 — 3 replies
        $t5 = $createTopic(
            $forumGeneral,
            'Best open source projects of 2025',
            $alice,
            'There have been so many exciting open source releases this year. '
            ."I've been particularly impressed by the progress in the AI tooling space — projects like Ollama and Open WebUI have matured enormously. "
            .'What open source projects have caught your attention in 2025?',
            Carbon::now()->subDays(8)->subHours(6)
        );
        $addReply($t5, $forumGeneral, $bob,
            'Huge fan of everything happening in the Rust ecosystem right now. '
            ."Zed editor has become my daily driver and it's breathtakingly fast. "
            .'The open source community really delivered this year.',
            Carbon::now()->subDays(7)->subHours(11));
        $addReply($t5, $forumGeneral, $charlie,
            "Apache Iceberg tooling has exploded — if you're in data engineering it's impossible to ignore. "
            .'Also shoutout to DuckDB which keeps shipping incredible features at an absurd pace. '
            .'The future of local-first analytics looks bright.',
            Carbon::now()->subDays(6)->subHours(4));
        $addReply($t5, $forumGeneral, $diana,
            'From the web side, the Svelte 5 release was a big deal. '
            .'The new runes API is a fundamentally better mental model for reactivity. '
            ."Also keeping a close eye on Bun — it's becoming a serious Node.js alternative.",
            Carbon::now()->subDays(5)->subHours(8));

        // ----------------------------------------------------------------
        // 6. Topics in "Suggestions"
        // ----------------------------------------------------------------

        // Topic 6: Dark mode — 2 replies
        $t6 = $createTopic(
            $forumSuggestion,
            'Add dark mode support',
            $bob,
            "I'd love to see a dark mode option for the forum. "
            .'Staring at a bright white background during late-night sessions is rough on the eyes. '
            .'A simple CSS toggle would make a huge difference in comfort.',
            Carbon::now()->subDays(15)->subHours(13)
        );
        $addReply($t6, $forumSuggestion, $alice,
            '+1 for dark mode! This is one of those features that once you have it you can never go back. '
            .'Even a basic implementation using the prefers-color-scheme media query would be a great start. '
            .'Happy to help with CSS if the dev team needs a hand.',
            Carbon::now()->subDays(14)->subHours(7));
        $addReply($t6, $forumSuggestion, $diana,
            "Noted! I'll pass this suggestion along to the admin team. "
            ."It's one of the most commonly requested features and there's clearly appetite for it here. "
            ."No promises on timeline but it's on the radar.",
            Carbon::now()->subDays(13)->subHours(5));

        // Topic 7: Mobile design — 3 replies
        $t7 = $createTopic(
            $forumSuggestion,
            'Mobile-friendly design improvements',
            $diana,
            "I've noticed the forum layout can be a bit cramped on smaller screens. "
            .'Navigation menus overlap on phones with narrow viewports and posting on mobile is tricky. '
            .'Has there been any thought given to a more responsive design?',
            Carbon::now()->subDays(11)->subHours(10)
        );
        $addReply($t7, $forumSuggestion, $charlie,
            'Totally agree. The reply editor especially struggles on mobile — the toolbar goes off-screen. '
            .'A collapsible toolbar or a simplified mobile editor would solve the worst pain points. '
            .'Happy to submit a PR if the codebase is open.',
            Carbon::now()->subDays(10)->subHours(15));
        $addReply($t7, $forumSuggestion, $bob,
            'The topic list is also a bit hard to tap accurately on mobile — the rows are quite close together. '
            .'Increasing touch target sizes would be a low-effort high-impact fix. '
            .'Material Design guidelines recommend at least 48px for interactive elements.',
            Carbon::now()->subDays(9)->subHours(12));
        $addReply($t7, $forumSuggestion, $diana,
            "Good points all around. I'll compile these into a formal suggestion document for the dev team. "
            .'The touch target sizing is something we can definitely address quickly. '
            .'Thanks for the detailed feedback, everyone!',
            Carbon::now()->subDays(8)->subHours(3));

        // ----------------------------------------------------------------
        // 7. Topics in "Help & Support"
        // ----------------------------------------------------------------

        // Topic 8: Avatar — 2 replies
        $t8 = $createTopic(
            $forumSupport,
            'How do I change my avatar?',
            $charlie,
            "I've been trying to update my profile picture but I can't find the option anywhere. "
            .'I looked in the profile settings and account preferences but nothing seems obvious. '
            .'Could someone point me in the right direction?',
            Carbon::now()->subDays(14)->subHours(9)
        );
        $addReply($t8, $forumSupport, $diana,
            "Hi Charlie! Go to your profile page (click your username at the top), then select 'Edit Profile'. "
            ."You'll see the avatar section about halfway down the page — you can either upload an image or link to an external URL. "
            .'Let me know if you have any trouble finding it!',
            Carbon::now()->subDays(14)->subHours(7));
        $addReply($t8, $forumSupport, $charlie,
            "Found it — thanks Diana! The 'Edit Profile' link was a bit hidden on mobile but I spotted it eventually. "
            .'Avatar is now set and looking great. '
            .'Marking this as resolved.',
            Carbon::now()->subDays(13)->subHours(20));

        // Topic 9: Email notifications — 1 reply
        $t9 = $createTopic(
            $forumSupport,
            'Email notifications not working',
            $alice,
            "I subscribed to a couple of topics but I haven't received any email notifications even though there have been new replies. "
            ."I checked my spam folder and there's nothing there either. "
            ."My email setting is set to 'All replies' so I'm not sure what's going wrong.",
            Carbon::now()->subDays(7)->subHours(14)
        );
        $addReply($t9, $forumSupport, $admin,
            'Hi Alice, sorry for the inconvenience! We had a brief issue with our mail server configuration that has now been resolved. '
            .'Could you try toggling your notification preference off and then back on to force a refresh of your subscription settings? '
            ."If the problem persists please let me know and I'll investigate your account directly.",
            Carbon::now()->subDays(7)->subHours(8));

        // Topic 10: Registration issues — 4 replies
        $t10 = $createTopic(
            $forumSupport,
            'Registration issues',
            $bob,
            'A friend of mine tried to register yesterday but kept getting an error after submitting the registration form. '
            ."The error message just said 'Something went wrong' which isn't very helpful. "
            .'Has anyone else experienced this? Is there a known issue with registration at the moment?',
            Carbon::now()->subDays(5)->subHours(16)
        );
        $addReply($t10, $forumSupport, $admin,
            'Hi Bob, thanks for flagging this. We did have a temporary database connectivity issue yesterday evening (UTC) that may have affected new registrations. '
            .'The issue has since been resolved. Could your friend try registering again? '
            .'If they continue to have problems please ask them to contact me at admin@example.com.',
            Carbon::now()->subDays(5)->subHours(12));
        $addReply($t10, $forumSupport, $bob,
            "Thanks for the quick response! I'll let them know and have them try again this evening. "
            .'Good to know it was a temporary issue and not something on their end. '
            ."I'll report back if the problem persists.",
            Carbon::now()->subDays(4)->subHours(18));
        $addReply($t10, $forumSupport, $admin,
            "Sounds good. I've also added some additional logging around the registration flow so we'll catch any future issues faster. "
            .'Please do report back — and if your friend manages to register successfully a confirmation here would be helpful for the record.',
            Carbon::now()->subDays(4)->subHours(14));
        $addReply($t10, $forumSupport, $bob,
            'Great news — my friend just registered successfully! Whatever the fix was it did the trick. '
            .'Thanks for the rapid turnaround on this, admin. '
            .'Marking this resolved.',
            Carbon::now()->subDays(3)->subHours(9));

        // ----------------------------------------------------------------
        // 8. Update Topic counters and last_post
        // ----------------------------------------------------------------
        $allTopics = Topic::all(); // reload fresh from DB

        foreach ($allTopics as $topic) {
            $posts = Post::where('topic_id', (string) $topic->_id)->orderBy('posted', 'asc')->get();
            $numReplies = max(0, $posts->count() - 1); // first post is the topic opener, not a reply
            $lastPost = $posts->last();

            $topic->num_replies = $numReplies;
            $topic->last_post = [
                'post_id' => (string) $lastPost->_id,
                'poster' => $lastPost->poster,
                'poster_id' => (string) $lastPost->poster_id,
                'time' => $lastPost->posted,
            ];
            $topic->save();
        }

        // ----------------------------------------------------------------
        // 9. Update Forum counters and last_post
        // ----------------------------------------------------------------
        $forums = Forum::all();

        foreach ($forums as $forum) {
            $topics = Topic::where('forum_id', (string) $forum->_id)->get();
            $numTopics = $topics->count();

            $totalPosts = 0;
            $lastPostData = null;
            $lastPostTime = null;

            foreach ($topics as $topic) {
                $posts = Post::where('topic_id', (string) $topic->_id)->orderBy('posted', 'asc')->get();
                $totalPosts += $posts->count();

                $lp = $posts->last();
                if ($lp && ($lastPostTime === null || $lp->posted > $lastPostTime)) {
                    $lastPostTime = $lp->posted;
                    $lastPostData = [
                        'post_id' => (string) $lp->_id,
                        'topic_id' => (string) $topic->_id,
                        'subject' => $topic->subject,
                        'poster' => $lp->poster,
                        'time' => $lp->posted,
                    ];
                }
            }

            $forum->num_topics = $numTopics;
            $forum->num_posts = $totalPosts;
            if ($lastPostData) {
                $forum->last_post = $lastPostData;
            }
            $forum->save();
        }

        // ----------------------------------------------------------------
        // 10. Update User num_posts
        // ----------------------------------------------------------------
        $allUsers = User::all();

        foreach ($allUsers as $user) {
            $count = Post::where('poster_id', (string) $user->_id)->count();
            $user->num_posts = $count;
            $user->save();
        }
    }
}
