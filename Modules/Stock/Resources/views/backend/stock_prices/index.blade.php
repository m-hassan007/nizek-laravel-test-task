@extends('backend.layouts.app')

@section('title', __('Stock Prices Management'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stock Prices Management</h4>
                    <div class="card-tools">
                        <a href="{{ route('backend.stock_prices.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Upload Excel File
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($companies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Company Symbol</th>
                                        <th>Company Name</th>
                                        <th>Latest Price</th>
                                        <th>Latest Date</th>
                                        <th>Total Records</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                        @php
                                            $latestPrice = \Modules\Stock\Entities\StockPrice::forCompany($company->company_symbol)
                                                ->latest()
                                                ->first();
                                            $totalRecords = \Modules\Stock\Entities\StockPrice::forCompany($company->company_symbol)
                                                ->count();
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $company->company_symbol }}</strong></td>
                                            <td>{{ $company->company_name ?: 'N/A' }}</td>
                                            <td>
                                                @if($latestPrice)
                                                    ${{ number_format($latestPrice->close_price, 2) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($latestPrice)
                                                    {{ $latestPrice->date->format('Y-m-d') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $totalRecords }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.stock_prices.show', $company->company_symbol) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form method="POST" action="{{ route('backend.stock_prices.destroy', $company->company_symbol) }}" 
                                                      style="display: inline-block;" 
                                                      onsubmit="return confirm('Are you sure you want to delete all data for {{ $company->company_symbol }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No stock data available. Upload an Excel file to get started.</p>
                            <a href="{{ route('backend.stock_prices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Upload Excel File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
