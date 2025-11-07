<?php
namespace OCA\ActivityTimeCalculator\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ApiController extends Controller {
    public function __construct($appName, IRequest $request) {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     */
    public function getActivityData($startDate, $endDate) {
        // Return mock data for now - we'll add calendar integration later
        $sampleData = [
            'Work' => 125400,
            'Meeting' => 72000, 
            'Personal' => 54000,
            'Sports' => 18000,
            'Development' => 86400
        ];

        return new DataResponse([
            'status' => 'success',
            'data' => $sampleData,
            'message' => 'Sample data - calendar integration coming soon',
            'metadata' => [
                'dateRange' => $startDate . ' to ' . $endDate,
                'totalCategories' => count($sampleData)
            ]
        ]);
    }
}
