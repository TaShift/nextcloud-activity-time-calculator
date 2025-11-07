<?php
namespace OCA\ActivityTimeCalculator\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCA\ActivityTimeCalculator\Service\ActivityTimeService;

class ApiController extends Controller {
    private $activityTimeService;
    private $userId;

    public function __construct($appName, IRequest $request, ActivityTimeService $activityTimeService, $userId) {
        parent::__construct($appName, $request);
        $this->activityTimeService = $activityTimeService;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function getActivityData($startDate, $endDate) {
        $result = $this->activityTimeService->calculateActivityTime($startDate, $endDate);
        
        if ($result['success']) {
            return new DataResponse([
                'status' => 'success',
                'data' => $result['data'],
                'message' => 'Calendar data retrieved successfully'
            ]);
        } else {
            return new DataResponse([
                'status' => 'error',
                'error' => $result['error'],
                'message' => 'Failed to retrieve calendar data'
            ], 500);
        }
    }
}
