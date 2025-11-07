<?php

declare(strict_types=1);

return [
    'routes' => [
        // Pagina principale
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        
        // API endpoints
        ['name' => 'api#getCalendarEvents', 'url' => '/api/events', 'verb' => 'GET'],
        ['name' => 'api#generateReport', 'url' => '/api/report', 'verb' => 'GET'],
    ]
];
