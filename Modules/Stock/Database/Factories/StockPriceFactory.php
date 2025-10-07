<?php

namespace Modules\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Stock\Entities\StockPrice;
use Carbon\Carbon;

class StockPriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockPrice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $basePrice = $this->faker->randomFloat(2, 10, 500);
        $volatility = $basePrice * 0.1; // 10% volatility

        return [
            'company_symbol' => $this->faker->randomElement(['AAPL', 'GOOGL', 'MSFT', 'TSLA', 'AMZN']),
            'company_name' => $this->faker->company,
            'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'open_price' => $basePrice + $this->faker->randomFloat(2, -$volatility, $volatility),
            'high_price' => $basePrice + $this->faker->randomFloat(2, 0, $volatility),
            'low_price' => $basePrice - $this->faker->randomFloat(2, 0, $volatility),
            'close_price' => $basePrice + $this->faker->randomFloat(2, -$volatility, $volatility),
            'adjusted_close' => $basePrice + $this->faker->randomFloat(2, -$volatility, $volatility),
            'volume' => $this->faker->numberBetween(1000000, 100000000),
        ];
    }

    /**
     * Create stock price for a specific company and date
     */
    public function forCompany($symbol, $date = null)
    {
        return $this->state(function (array $attributes) use ($symbol, $date) {
            return [
                'company_symbol' => $symbol,
                'date' => $date ?: Carbon::now()->format('Y-m-d'),
            ];
        });
    }
}
