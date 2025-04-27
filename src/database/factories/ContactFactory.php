<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
// 忘れない！
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' =>$this->faker->numberBetween(1,5),
            // カテゴリが5つあるので
            'first_name' =>$this->faker->lastName(),
            // 苗字
            'last_name' =>$this->faker->firstName(),
            // 名前
            'gender'=>$this->faker->randomElement([1,2,3]),
            // 指定した配列からランダムに1つの値を選ぶ
            'email' =>$this->faker->safeEmail(),
            'tell' =>$this->faker->phoneNumber(),
            'address' =>$this->faker->city() . $this->faker->streetAddress(),
            // ランダムな都市名とランダムな住所をドットを使って結合、1つの文字列を作る
            'building' =>$this->faker->secondaryAddress(),
            'detail' =>$this->faker->text(120),
            // DatabaseSeeder.phpの記入もあるよ
        ];
    }
}
