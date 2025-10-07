<?php

namespace Modules\Stock\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Stock\Entities\StockPrice;
use App\Jobs\ProcessStockExcelFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StockPricesController extends Controller
{
    /**
     * Display a listing of stock prices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = StockPrice::select('company_symbol', 'company_name')
            ->distinct()
            ->orderBy('company_symbol')
            ->get();

        return view('stock::backend.stock_prices.index', compact('companies'));
    }

    /**
     * Show the form for uploading Excel file.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stock::backend.stock_prices.create');
    }

    /**
     * Store a newly uploaded Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'company_symbol' => 'required|string|max:10',
            'company_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Store the uploaded file
            $file = $request->file('excel_file');
            $fileName = 'stock_data_' . time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('temp', $fileName);

            // Dispatch the job to process the Excel file in the background
            ProcessStockExcelFile::dispatch(
                $filePath,
                $request->company_symbol,
                $request->company_name
            );

            return redirect()->route('backend.stock_prices.index')
                ->with('success', 'Excel file uploaded successfully. Processing will begin shortly.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to upload file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified company's stock prices.
     *
     * @param  string  $symbol
     * @return \Illuminate\Http\Response
     */
    public function show($symbol)
    {
        $stockPrices = StockPrice::forCompany($symbol)
            ->latest()
            ->paginate(50);

        $companyName = StockPrice::where('company_symbol', $symbol)
            ->value('company_name');

        return view('stock::backend.stock_prices.show', compact('stockPrices', 'symbol', 'companyName'));
    }

    /**
     * Remove the specified stock prices for a company.
     *
     * @param  string  $symbol
     * @return \Illuminate\Http\Response
     */
    public function destroy($symbol)
    {
        try {
            StockPrice::forCompany($symbol)->delete();

            return redirect()->route('backend.stock_prices.index')
                ->with('success', "All stock prices for {$symbol} have been deleted successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete stock prices: ' . $e->getMessage());
        }
    }
}
