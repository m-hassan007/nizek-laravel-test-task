<?php

return [
    'name' => 'Stock',
    'excel_max_size' => 10240, // 10MB in KB
    'excel_allowed_types' => ['xlsx', 'xls'],
    'batch_size' => 1000,
    'temp_storage_path' => 'temp',
];
