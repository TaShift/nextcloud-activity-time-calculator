<?php

namespace OCA\ActivityTimeCalculator\Service;

use OCP\Calendar\IManager;
use OCP\IUserSession;

class ActivityTimeService {
    private $calendarManager;
    private $userSession;

    public function __construct(IManager $calendarManager, IUserSession $userSession) {
        $this->calendarManager = $calendarManager;
        $this->userSession = $userSession;
    }

    public function calculateActivityTime($startDate, $endDate) {
        try {
            $user = $this->userSession->getUser();
            if (!$user) {
                return ['success' => false, 'error' => 'User not logged in'];
            }

            $principalUri = 'principals/users/' . $user->getUID();
            $calendars = $this->calendarManager->getCalendarsForPrincipal($principalUri);
            
            $totalTimeByCategory = [];
            $eventCount = 0;

            foreach ($calendars as $calendar) {
                $searchResults = $calendar->search('', [], [], $startDate, $endDate);
                
                foreach ($searchResults as $event) {
                    $category = $this->extractCategory($event);
                    $duration = $this->calculateEventDuration($event);
                    
                    if (!isset($totalTimeByCategory[$category])) {
                        $totalTimeByCategory[$category] = 0;
                    }
                    $totalTimeByCategory[$category] += $duration;
                    $eventCount++;
                }
            }

            return [
                'success' => true, 
                'data' => $totalTimeByCategory,
                'eventCount' => $eventCount,
                'calendarCount' => count($calendars)
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function extractCategory($event) {
        // Try to get category from event properties
        if (isset($event['categories']) && !empty($event['categories'])) {
            return is_array($event['categories']) ? $event['categories'][0] : $event['categories'];
        }
        
        if (isset($event['classification'])) {
            return $event['classification'];
        }
        
        // Use calendar name as fallback category
        if (isset($event['calendar-key'])) {
            return $event['calendar-key'];
        }
        
        return 'Uncategorized';
    }

    private function calculateEventDuration($event) {
        if (isset($event['start'], $event['end'])) {
            try {
                $start = new \DateTime($event['start']);
                $end = new \DateTime($event['end']);
                return $end->getTimestamp() - $start->getTimestamp();
            } catch (\Exception $e) {
                // Fallback for all-day events
                return 3600; // 1 hour default
            }
        }
        return 0;
    }
}
