@extends('backend.layouts.app')

@section('title', __('Upload Stock Data'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Upload Stock Data from Excel</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('backend.stock_prices.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_symbol" class="form-label">Company Symbol <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('company_symbol') is-invalid @enderror" 
                                           id="company_symbol" 
                                           name="company_symbol" 
                                           value="{{ old('company_symbol') }}" 
                                           placeholder="e.g., AAPL, GOOGL, MSFT"
                                           maxlength="10"
                                           required>
                                    @error('company_symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Enter the stock symbol (max 10 characters)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" 
                                           class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name') }}" 
                                           placeholder="e.g., Apple Inc."
                                           maxlength="255">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional: Enter the full company name</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Excel File <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Upload Excel file (max 10MB). Expected format: Date, Open, High, Low, Close, Volume
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Excel File Format Requirements:</h6>
                            <ul class="mb-0">
                                <li><strong>Column A:</strong> Date (YYYY-MM-DD format)</li>
                                <li><strong>Column B:</strong> Open Price</li>
                                <li><strong>Column C:</strong> High Price</li>
                                <li><strong>Column D:</strong> Low Price</li>
                                <li><strong>Column E:</strong> Close Price (required)</li>
                                <li><strong>Column F:</strong> Volume (optional)</li>
                            </ul>
                            <p class="mb-0 mt-2">
                                <strong>Note:</strong> The first row should contain headers. Processing will start automatically after upload.
                            </p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('backend.stock_prices.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload & Process
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
