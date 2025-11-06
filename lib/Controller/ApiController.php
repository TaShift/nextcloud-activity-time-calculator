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
use OCP\ICalendarManager;
use OCP\IUserSession;

class ApiController extends Controller {
    
    private $calendarManager;
    private $userSession;

    public function __construct(
        string $appName, 
        IRequest $request,
        ICalendarManager $calendarManager,
        IUserSession $userSession
    ) {
        parent::__construct($appName, $request);
        $this->calendarManager = $calendarManager;
        $this->userSession = $userSession;
    }

    /**
     * @NoAdminRequired
     */
    public function getActivityData(string $startDate, string $endDate): DataResponse {
        try {
            // Get current logged-in user
            $user = $this->userSession->getUser();
            if (!$user) {
                return new DataResponse([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get all calendars for the current user
            $calendars = $this->calendarManager->getCalendarsForPrincipal(
                'principals/users/' . $user->getUID()
            );

            $categoryTime = [];
            $totalEvents = 0;
            
            foreach ($calendars as $calendar) {
                try {
                    // Search for events in the date range
                    $events = $calendar->search('', ['SUMMARY', 'CATEGORIES', 'DESCRIPTION'], [
                        'start' => new \DateTime($startDate),
                        'end' => new \DateTime($endDate . ' 23:59:59') // Include entire end day
                    ]);

                    $totalEvents += count($events);

                    foreach ($events as $event) {
                        $categories = $this->extractCategories($event);
                        $duration = $this->calculateDuration($event);
                        
                        foreach ($categories as $category) {
                            if (!isset($categoryTime[$category])) {
                                $categoryTime[$category] = 0;
                            }
                            $categoryTime[$category] += $duration;
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue with other calendars
                    error_log("Calendar error: " . $e->getMessage());
                    continue;
                }
            }

            // Sort by time descending
            arsort($categoryTime);

            return new DataResponse([
                'status' => 'success',
                'data' => $categoryTime,
                'metadata' => [
                    'totalEvents' => $totalEvents,
                    'calendarsProcessed' => count($calendars),
                    'dateRange' => $startDate . ' to ' . $endDate
                ],
                'message' => 'Calendar data analyzed successfully'
            ]);
            
        } catch (\Exception $e) {
            return new DataResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function extractCategories(array $event): array {
        $categories = [];
        
        if (isset($event['CATEGORIES'])) {
            $categoryString = $event['CATEGORIES'];
            if (is_string($categoryString)) {
                $categories = array_map('trim', explode(',', $categoryString));
            } elseif (is_array($categoryString)) {
                $categories = $categoryString;
            }
        }
        
        return empty($categories) ? ['Uncategorized'] : $categories;
    }

    private function calculateDuration(array $event): int {
        if (!isset($event['DTSTART']) || !isset($event['DTEND'])) {
            return 0;
        }

        try {
            $start = new \DateTime($event['DTSTART']);
            $end = new \DateTime($event['DTEND']);
            
            return $end->getTimestamp() - $start->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
