<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

trait ActiveUserHelper
{
    protected $users = [];

    protected $topic_weight = 4;
    protected $reply_weight = 1;
    protected $pass_days = 7;
    protected $user_number = 6;

    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65 * 60;

    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        $activeUsers = $this->calculateActiveUsers();

        $this->cacheActiveUsers($activeUsers);
    }

    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        $users = Arr::sort($this->users, function ($user) {
            return $user['score'];
        });

        $users = array_reverse($users, true);

        $users = array_slice($users, 0, $this->user_number, true);

        $activeUsers = collect();

        foreach ($users as $userId => $user) {
            if ($user) {
                $activeUsers->push($user);
            }
        }

        return $activeUsers;
    }

    private function calculateTopicScore()
    {
        $topicUsers = Topic::query()
            ->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        foreach ($topicUsers as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    private function calculateReplyScore()
    {
        $replyUsers = Reply::query()
            ->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        foreach ($replyUsers as $value) {
            $replyScore = $value->reply_count * $this->reply_weight;
            if (isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $replyScore;
            } else {
                $this->users[$value->user_id]['score'] = $replyScore;
            }
        }
    }

    private function cacheActiveUsers($activeUsers)
    {
        Cache::put($this->cache_key, $activeUsers, $this->cache_expire_in_seconds);
    }
}
