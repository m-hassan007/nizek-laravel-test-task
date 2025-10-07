@extends('backend.layouts.app')

@section('title', __('Stock Prices - :symbol', ['symbol' => $symbol]))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Stock Prices for {{ $symbol }}
                        @if($companyName)
                            <small class="text-muted">({{ $companyName }})</small>
                        @endif
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('backend.stock_prices.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($stockPrices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Open</th>
                                        <th>High</th>
                                        <th>Low</th>
                                        <th>Close</th>
                                        <th>Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockPrices as $price)
                                        <tr>
                                            <td>{{ $price->date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($price->open_price)
                                                    ${{ number_format($price->open_price, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($price->high_price)
                                                    ${{ number_format($price->high_price, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($price->low_price)
                                                    ${{ number_format($price->low_price, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><strong>${{ number_format($price->close_price, 2) }}</strong></td>
                                            <td>
                                                @if($price->volume)
                                                    {{ number_format($price->volume) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $stockPrices->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No stock price data found for {{ $symbol }}.</p>
                            <a href="{{ route('backend.stock_prices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Upload Data
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
