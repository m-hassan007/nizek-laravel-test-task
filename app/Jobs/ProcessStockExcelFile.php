<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Stock\Entities\StockPrice;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ProcessStockExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $companySymbol;
    protected $companyName;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param string $companySymbol
     * @param string $companyName
     */
    public function __construct($filePath, $companySymbol, $companyName = null)
    {
        $this->filePath = $filePath;
        $this->companySymbol = $companySymbol;
        $this->companyName = $companyName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Starting Excel processing for company: {$this->companySymbol}");

            // Load the Excel file
            $spreadsheet = IOFactory::load(storage_path('app/' . $this->filePath));
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $processedCount = 0;
            $batchSize = 1000; // Process in batches for better memory management
            $batch = [];

            // Skip header row (assuming row 1 is header)
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $data = $this->extractRowData($worksheet, $row);
                    
                    if ($data) {
                        $batch[] = $data;
                        
                        // Insert batch when it reaches the batch size
                        if (count($batch) >= $batchSize) {
                            $this->insertBatch($batch);
                            $processedCount += count($batch);
                            $batch = [];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error processing row {$row}: " . $e->getMessage());
                    continue;
                }
            }

            // Insert remaining records
            if (!empty($batch)) {
                $this->insertBatch($batch);
                $processedCount += count($batch);
            }

            Log::info("Excel processing completed for company: {$this->companySymbol}. Processed {$processedCount} records.");

            // Clean up the uploaded file
            Storage::delete($this->filePath);

        } catch (\Exception $e) {
            Log::error("Excel processing failed for company: {$this->companySymbol}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract data from a single row
     */
    private function extractRowData($worksheet, $row)
    {
        $date = $worksheet->getCell("A{$row}")->getValue();
        $open = $worksheet->getCell("B{$row}")->getValue();
        $high = $worksheet->getCell("C{$row}")->getValue();
        $low = $worksheet->getCell("D{$row}")->getValue();
        $close = $worksheet->getCell("E{$row}")->getValue();
        $volume = $worksheet->getCell("F{$row}")->getValue();

        // Skip empty rows
        if (empty($date) || empty($close)) {
            return null;
        }

        // Convert Excel date to Carbon date
        if (is_numeric($date)) {
            $date = Date::excelToDateTimeObject($date)->format('Y-m-d');
        } else {
            $date = Carbon::parse($date)->format('Y-m-d');
        }

        return [
            'company_symbol' => $this->companySymbol,
            'company_name' => $this->companyName,
            'date' => $date,
            'open_price' => $open ?: null,
            'high_price' => $high ?: null,
            'low_price' => $low ?: null,
            'close_price' => $close,
            'adjusted_close' => $close, // Use close price if adjusted close is not available
            'volume' => $volume ?: null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Insert batch of records with conflict resolution
     */
    private function insertBatch($batch)
    {
        // Use upsert to handle duplicate dates for the same company
        foreach ($batch as $record) {
            StockPrice::updateOrCreate(
                [
                    'company_symbol' => $record['company_symbol'],
                    'date' => $record['date'],
                ],
                $record
            );
        }
    }
}
