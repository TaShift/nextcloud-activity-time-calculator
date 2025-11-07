<?php
/**
 * @copyright Copyright (c) 2024 Your Name
 * @license GNU AGPL version 3 or any later version
 */

return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'api#getActivityData', 'url' => '/api/activity-data', 'verb' => 'GET'],
    ]
];
