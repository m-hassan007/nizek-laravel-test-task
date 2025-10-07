<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Stock\Entities\StockPrice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class StockAnalysisController extends Controller
{
    /**
     * Get stock price change between two custom dates
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomDateChange(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'symbol' => 'required|string|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = StockPrice::getPriceChange(
                $request->symbol,
                $request->start_date,
                $request->end_date
            );

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified symbol and date range'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Stock price analysis retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock price change for predefined periods
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPeriodChange(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'symbol' => 'required|string|max:10',
            'period' => 'required|string|in:1D,1M,3M,6M,YTD,1Y,3Y,5Y,10Y,MAX',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = StockPrice::getPeriodData($request->symbol, $request->period);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified symbol and period'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'period' => $request->period,
                'message' => 'Stock price analysis retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available periods for a symbol
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllPeriods(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'symbol' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periods = ['1D', '1M', '3M', '6M', 'YTD', '1Y', '3Y', '5Y', '10Y', 'MAX'];
            $results = [];

            foreach ($periods as $period) {
                $data = StockPrice::getPeriodData($request->symbol, $period);
                if ($data) {
                    $results[$period] = $data;
                }
            }

            if (empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified symbol'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'All period data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available companies
     *
     * @return JsonResponse
     */
    public function getCompanies(): JsonResponse
    {
        try {
            $companies = StockPrice::select('company_symbol', 'company_name')
                ->distinct()
                ->orderBy('company_symbol')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $companies,
                'message' => 'Companies retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest stock price for a company
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLatestPrice(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'symbol' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $latestPrice = StockPrice::forCompany($request->symbol)
                ->latest()
                ->first();

            if (!$latestPrice) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified symbol'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $latestPrice,
                'message' => 'Latest stock price retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
