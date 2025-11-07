<?php

declare(strict_types=1);

namespace OCA\ActivityTimeCalculator\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;
use OCA\ActivityTimeCalculator\Service\ReportService;

class ApiController extends Controller {

    private IUserSession $userSession;
    private ReportService $reportService;

    public function __construct(
        string $appName,
        IRequest $request,
        IUserSession $userSession,
        ReportService $reportService
    ) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->reportService = $reportService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getCalendarEvents(): JSONResponse {
        try {
            // Verify user is authenticated
            if (!$this->userSession->isLoggedIn()) {
                return new JSONResponse([
                    'error' => 'Not authenticated',
                    'code' => 401
                ], 401);
            }

            $user = $this->userSession->getUser();
            $userId = $user->getUID();
            
            \OCP\Util::writeLog('activitytimecalculator', "Fetching calendar events for user: " . $userId, \OCP\Util::INFO);

            $events = $this->reportService->getCalendarEvents($userId);
            
            \OCP\Util::writeLog('activitytimecalculator', "Found " . count($events) . " events for user: " . $userId, \OCP\Util::INFO);

            return new JSONResponse([
                'success' => true,
                'events' => $events,
                'totalEvents' => count($events)
            ]);

        } catch (\Exception $e) {
            \OCP\Util::writeLog('activitytimecalculator', "Error in getCalendarEvents: " . $e->getMessage(), \OCP\Util::ERROR);
            
            return new JSONResponse([
                'error' => 'Failed to fetch calendar events: ' . $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function generateReport(): JSONResponse {
        try {
            if (!$this->userSession->isLoggedIn()) {
                return new JSONResponse([
                    'error' => 'Not authenticated',
                    'code' => 401
                ], 401);
            }

            $user = $this->userSession->getUser();
            $userId = $user->getUID();

            \OCP\Util::writeLog('activitytimecalculator', "Generating report for user: " . $userId, \OCP\Util::INFO);

            $report = $this->reportService->generateTimeReport($userId);
            
            \OCP\Util::writeLog('activitytimecalculator', "Report generated for user: " . $userId, \OCP\Util::INFO);

            return new JSONResponse([
                'success' => true,
                'report' => $report
            ]);

        } catch (\Exception $e) {
            \OCP\Util::writeLog('activitytimecalculator', "Error in generateReport: " . $e->getMessage(), \OCP\Util::ERROR);
            
            return new JSONResponse([
                'error' => 'Failed to generate report: ' . $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }
}
