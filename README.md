# Stock Price Management System

A comprehensive Laravel application for managing stock price data from Excel files with advanced analysis capabilities and RESTful APIs.

## Features

### Core Functionality
- **Excel File Upload**: Upload large Excel files containing historical stock price data
- **Background Processing**: Large Excel files are processed in the background using Laravel queues
- **Stock Price Analysis**: Calculate price changes and percentages for custom date ranges
- **Predefined Periods**: Support for standard periods (1D, 1M, 3M, 6M, YTD, 1Y, 3Y, 5Y, 10Y, MAX)
- **RESTful APIs**: Complete API endpoints for all stock analysis operations
- **Admin Interface**: Web-based interface for managing stock data

### Technical Features
- **Modular Architecture**: Built using Laravel Modules for clean separation
- **Database Optimization**: Optimized indexes for handling large datasets
- **SOLID Principles**: Clean, maintainable code following SOLID principles
- **Docker Support**: Ready-to-use Docker configuration with Laravel Sail
- **Queue Processing**: Background job processing for Excel files
- **Comprehensive Testing**: Postman collection included for API testing

## Prerequisites

- PHP 8.0.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0
- Redis (for queue processing)
- Docker & Docker Compose (optional)

## Installation

### Option 1: Docker (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd nizek-laravel-test-task
   ```

2. **Set up environment**
   ```bash
   cp .env.example .env
   ```

3. **Start with Laravel Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Install dependencies**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ```

5. **Run migrations and seeders**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

6. **Generate application key**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

7. **Start the queue worker**
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

### Option 2: Manual Installation

1. **Clone and setup**
   ```bash
   git clone <repository-url>
   cd nizek-laravel-test-task
   composer install
   npm install
   ```

2. **Environment configuration**
   ```bash
   cp .env.example .env
   # Update .env with your database and Redis configuration
   ```

3. **Database setup**
   ```bash
   php artisan migrate --seed
   php artisan key:generate
   ```

4. **Build assets**
   ```bash
   npm run dev
   ```

5. **Start services**
   ```bash
   # Start the application
   php artisan serve
   
   # Start queue worker (in another terminal)
   php artisan queue:work
   ```

## Configuration

### Environment Variables

Update your `.env` file with the following configurations:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_management
DB_USERNAME=root
DB_PASSWORD=

# Queue Configuration
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# File Upload
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
```

### Queue Configuration

For production, configure a proper queue driver:

```env
QUEUE_CONNECTION=database  # or redis, sqs, etc.
```

## Usage

### Web Interface

1. **Access Admin Panel**
   - URL: `http://localhost:8000/admin`
   - Login with admin credentials
   - Navigate to "Stock Prices" section

2. **Upload Excel Files**
   - Click "Upload Excel File"
   - Provide company symbol and name
   - Select Excel file (max 10MB)
   - Processing begins automatically in background

### Excel File Format

Your Excel file should follow this format:

| Column A | Column B | Column C | Column D | Column E | Column F |
|----------|----------|----------|----------|----------|----------|
| Date     | Open     | High     | Low      | Close    | Volume   |
| 2024-01-01 | 150.00 | 155.00 | 148.00 | 152.50 | 50000000 |

**Requirements:**
- First row should contain headers
- Date format: YYYY-MM-DD
- Close price is required, others are optional
- Maximum file size: 10MB

### API Usage

#### Base URL
```
http://localhost:8000/api/stock
```

#### Available Endpoints

1. **Get Available Companies**
   ```bash
   GET /api/stock/companies
   ```

2. **Get Latest Stock Price**
   ```bash
   GET /api/stock/latest-price?symbol=AAPL
   ```

3. **Custom Date Range Analysis**
   ```bash
   GET /api/stock/custom-date-change?symbol=AAPL&start_date=2024-01-01&end_date=2024-01-15
   ```

4. **Period-based Analysis**
   ```bash
   GET /api/stock/period-change?symbol=AAPL&period=1Y
   ```
   Supported periods: `1D`, `1M`, `3M`, `6M`, `YTD`, `1Y`, `3Y`, `5Y`, `10Y`, `MAX`

5. **Get All Periods Data**
   ```bash
   GET /api/stock/all-periods?symbol=AAPL
   ```

#### Example API Response

```json
{
    "success": true,
    "data": {
        "start_price": "145.50",
        "end_price": "152.50",
        "change": "7.00",
        "percentage": "4.81",
        "start_date": "2024-01-01",
        "end_date": "2024-01-15"
    },
    "message": "Stock price analysis retrieved successfully"
}
```

### Postman Collection

Import the included `Stock_Price_API_Collection.postman_collection.json` file into Postman for easy API testing.

## Database Schema

### Stock Prices Table

```sql
CREATE TABLE stock_prices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_symbol VARCHAR(10) NOT NULL,
    company_name VARCHAR(255) NULL,
    date DATE NOT NULL,
    open_price DECIMAL(15,4) NULL,
    high_price DECIMAL(15,4) NULL,
    low_price DECIMAL(15,4) NULL,
    close_price DECIMAL(15,4) NOT NULL,
    adjusted_close DECIMAL(15,4) NULL,
    volume BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_company_date (company_symbol, date),
    INDEX idx_date (date),
    INDEX idx_symbol_date_close (company_symbol, date, close_price),
    INDEX idx_date_symbol (date, company_symbol)
);
```

## Performance Optimization

### Database Optimization
- Composite indexes for efficient queries
- Batch processing for large Excel files
- Proper data types and constraints

### Memory Management
- Chunked processing for large datasets
- Background job processing
- Configurable batch sizes

### Caching
- Redis integration for queue processing
- Database query optimization
- Efficient data retrieval patterns

## Development

### Running Tests
```bash
./vendor/bin/sail test
# or
php artisan test
```

### Code Style
```bash
composer fix-cs
```

### Clear All Cache
```bash
composer clear-all
```

## Troubleshooting

### Common Issues

1. **Queue Jobs Not Processing**
   - Ensure Redis is running
   - Check queue worker is active: `php artisan queue:work`
   - Verify queue configuration in `.env`

2. **Excel Upload Fails**
   - Check file size limits in PHP configuration
   - Verify file format matches requirements
   - Ensure sufficient disk space

3. **API Returns 404**
   - Run `php artisan route:cache` to refresh routes
   - Check API routes are properly registered

### Logs
Check application logs in `storage/logs/laravel.log` for detailed error information.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the GPL-3.0-or-later License.

## Support

For support and inquiries, please contact the development team or create an issue in the repository.

---

**Note**: This application is designed to handle large datasets efficiently. For production deployment, ensure proper server resources and monitoring are in place.