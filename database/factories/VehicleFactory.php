<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'car',
            'name' => $this->faker->name,
            'release_year' => $this->faker->year,
            'color' =>  $this->faker->colorName,
            'price' => $this->faker->randomNumber(9),
            'engine' => 'engine1',
            'passenger_capacity' => $this->faker->randomNumber(2),
            'car_type' => 'carType1'
        ];
    }
}
