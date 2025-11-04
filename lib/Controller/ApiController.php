<?php
/**
 * @copyright Copyright (c) 2024 Il Tuo Nome
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ActivityTimeCalculator\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\ICalendarManager;

class ApiController extends Controller {
    
    private $calendarManager;

    public function __construct(string $appName, IRequest $request, ICalendarManager $calendarManager) {
        parent::__construct($appName, $request);
        $this->calendarManager = $calendarManager;
    }

    /**
     * @NoAdminRequired
     */
    public function getActivityData(string $startDate, string $endDate): DataResponse {
        try {
            // Per ora restituiamo dati di esempio
            $sampleData = [
                'Work' => 125400, // secondi
                'Meeting' => 72000,
                'Personal' => 54000,
                'Sports' => 18000
            ];

            return new DataResponse([
                'status' => 'success',
                'data' => $sampleData,
                'message' => 'Calendar integration coming soon!'
            ]);
            
        } catch (\Exception $e) {
            return new DataResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
