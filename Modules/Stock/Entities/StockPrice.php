<?php

namespace Modules\Stock\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StockPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_symbol',
        'company_name',
        'date',
        'open_price',
        'high_price',
        'low_price',
        'close_price',
        'adjusted_close',
        'volume',
    ];

    protected $casts = [
        'date' => 'date',
        'open_price' => 'decimal:4',
        'high_price' => 'decimal:4',
        'low_price' => 'decimal:4',
        'close_price' => 'decimal:4',
        'adjusted_close' => 'decimal:4',
        'volume' => 'integer',
    ];

    /**
     * Scope to get prices for a specific company
     */
    public function scopeForCompany($query, $symbol)
    {
        return $query->where('company_symbol', $symbol);
    }

    /**
     * Scope to get prices within a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get the latest price for a company
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    /**
     * Get price change percentage between two dates
     */
    public static function getPriceChange($symbol, $startDate, $endDate)
    {
        $startPrice = self::forCompany($symbol)
            ->where('date', $startDate)
            ->value('close_price');

        $endPrice = self::forCompany($symbol)
            ->where('date', $endDate)
            ->value('close_price');

        if (!$startPrice || !$endPrice) {
            return null;
        }

        $change = $endPrice - $startPrice;
        $percentage = ($change / $startPrice) * 100;

        return [
            'start_price' => $startPrice,
            'end_price' => $endPrice,
            'change' => $change,
            'percentage' => round($percentage, 2),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Get price data for predefined periods
     */
    public static function getPeriodData($symbol, $period)
    {
        $endDate = Carbon::now();
        $startDate = self::calculatePeriodStartDate($period, $endDate);

        return self::getPriceChange($symbol, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
    }

    /**
     * Calculate start date for different periods
     */
    private static function calculatePeriodStartDate($period, $endDate)
    {
        switch (strtoupper($period)) {
            case '1D':
                return $endDate->copy()->subDay();
            case '1M':
                return $endDate->copy()->subMonth();
            case '3M':
                return $endDate->copy()->subMonths(3);
            case '6M':
                return $endDate->copy()->subMonths(6);
            case 'YTD':
                return $endDate->copy()->startOfYear();
            case '1Y':
                return $endDate->copy()->subYear();
            case '3Y':
                return $endDate->copy()->subYears(3);
            case '5Y':
                return $endDate->copy()->subYears(5);
            case '10Y':
                return $endDate->copy()->subYears(10);
            case 'MAX':
                // Get the earliest date for this symbol
                $earliestDate = self::forCompany($symbol)->min('date');
                return $earliestDate ? Carbon::parse($earliestDate) : $endDate->copy()->subYear();
            default:
                return $endDate->copy()->subMonth();
        }
    }
}
