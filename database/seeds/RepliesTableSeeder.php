<?php

use App\Models\Reply;
use Illuminate\Database\Seeder;

class RepliesTableSeeder extends Seeder
{
    public function run()
    {
        factory(Reply::class)->times(1000)->create();
    }
}
