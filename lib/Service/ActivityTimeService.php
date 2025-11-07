<?php

namespace OCA\ActivityTimeCalculator\Service;

use OCP\Calendar\IManager;
use OCP\IConfig;

class ActivityTimeService {
    private $calendarManager;
    private $config;
    private $userId;

    public function __construct(IManager $calendarManager, IConfig $config, $userId) {
        $this->calendarManager = $calendarManager;
        $this->config = $config;
        $this->userId = $userId;
    }

    public function calculateActivityTime($startDate, $endDate, $categories = []) {
        try {
            $principal = 'principals/users/' . $this->userId;
            $calendars = $this->calendarManager->getCalendarsForPrincipal($principal);
            
            $events = [];
            $totalTimeByCategory = [];

            foreach ($calendars as $calendar) {
                $searchResults = $calendar->search('', [], [], $startDate, $endDate);
                if (is_array($searchResults)) {
                    $events = array_merge($events, $searchResults);
                }
            }

            foreach ($events as $event) {
                $category = $this->extractCategory($event);
                $duration = $this->calculateEventDuration($event);
                
                if (!isset($totalTimeByCategory[$category])) {
                    $totalTimeByCategory[$category] = 0;
                }
                $totalTimeByCategory[$category] += $duration;
            }

            return ['success' => true, 'data' => $totalTimeByCategory];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function extractCategory($event) {
        return isset($event['categories']) ? $event['categories'][0] : 'Uncategorized';
    }

    private function calculateEventDuration($event) {
        if (isset($event['start'], $event['end'])) {
            $start = new \DateTime($event['start']);
            $end = new \DateTime($event['end']);
            return $end->getTimestamp() - $start->getTimestamp();
        }
        return 0;
    }
}
