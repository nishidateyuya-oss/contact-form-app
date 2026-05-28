<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Fakerの日本語インスタンスを取得
        $faker = \Faker\Factory::create('ja_JP');

        return [
            // 既存のカテゴリーからランダムに1つ選んでIDをセット
            'category_id' => Category::pluck('id')->random(),
            'first_name'  => $faker->lastName,  
            'last_name'   => $faker->firstName, 
            
            'gender'      => $this->faker->numberBetween(1, 3),
            'email'       => $this->faker->safeEmail,
            
            // 日本風の電話番号
            'tel'         => $this->faker->boolean(50) 
                                ? '0' . $this->faker->numberBetween(10, 99) . $this->faker->numberBetween(1000, 9999) . $this->faker->numberBetween(1000, 9999)
                                : '0' . $this->faker->numberBetween(3, 9) . $this->faker->numberBetween(100, 999) . $this->faker->numberBetween(1000, 9999),
            
            'address'     => $faker->prefecture . $faker->city . $faker->streetAddress,
            'building'    => $this->faker->boolean(70) ? $faker->secondaryAddress : null,
            'detail'      => $faker->realText(100),
        ];
    }
}