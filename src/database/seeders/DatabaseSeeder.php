<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
// 忘れない！
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
       Contact::factory(35)->create();
    //  モデルファクトリなのでこのコード。解答とは違うが作れてる。

    }
}
