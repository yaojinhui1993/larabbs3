<?php

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        factory(Topic::class)->times(100)->create();
    }
}
