<?php
/**
 * @copyright Copyright (c) 2024 Your Name
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ActivityTimeCalculator\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ApiController extends Controller {
    
    public function __construct(string $appName, IRequest $request) {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     */
    public function getActivityData(string $startDate, string $endDate): DataResponse {
        try {
            // Return simple test data first
            $sampleData = [
                'Work' => 125400,    // 34h 50m
                'Meeting' => 72000,  // 20h
                'Personal' => 54000, // 15h
                'Sports' => 18000    // 5h
            ];

            return new DataResponse([
                'status' => 'success',
                'data' => $sampleData,
                'message' => 'Test data - calendar integration coming soon'
            ]);
            
        } catch (\Exception $e) {
            return new DataResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
