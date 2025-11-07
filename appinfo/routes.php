<?php
/**
 * @copyright Copyright (c) 2024 Your Name
 * @license GNU AGPL version 3 or any later version
 */

return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'page#calculateTime', 'url' => '/api/calculate-time', 'verb' => 'GET'],
    ]
];
