<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => 1,
            'land_id' => 1,
            'priceperanna' => $this->faker->numberBetween(1000, 9000),
            'nepali_date' => $this->faker->date('Y-m-d', 'now'),
            'income' =>$this->faker->numberBetween(1000, 9000),
            'expenses' => $this->faker->numberBetween(1000, 9000),
            'totalpaidamount' => $this->faker->numberBetween(1000, 9000),
            'commission_rate' => $this->faker->numberBetween(1000, 9000),
            'totalcommission' => $this->faker->numberBetween(1000, 9000),
            'totalcommisionafterrate' => $this->faker->numberBetween(1000, 9000),
            'photo' => $this->faker->imageUrl(640, 480),
        ];
    }
}
