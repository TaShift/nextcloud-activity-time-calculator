<?php

declare(strict_types=1);

namespace OCA\ActivityTimeCalculator\Service;

use OCP\IConfig;
use OCP\IUserManager;
use OCP\Calendar\IManager as ICalendarManager;

class ReportService {

    private IConfig $config;
    private IUserManager $userManager;
    private ICalendarManager $calendarManager;

    public function __construct(
        IConfig $config,
        IUserManager $userManager,
        ICalendarManager $calendarManager
    ) {
        $this->config = $config;
        $this->userManager = $userManager;
        $this->calendarManager = $calendarManager;
    }

    public function getCalendarEvents(string $userId): array {
        try {
            \OCP\Util::writeLog('activitytimecalculator', "Getting calendar events for user: " . $userId, \OCP\Util::INFO);

            // Get user's password or app password from config
            $userPassword = $this->getUserPassword($userId);
            
            if (!$userPassword) {
                throw new \Exception('User password not configured');
            }

            // Use the JavaScript service via direct CalDAV calls
            $events = $this->fetchEventsViaCalDAV($userId, $userPassword);
            
            \OCP\Util::writeLog('activitytimecalculator', "Retrieved " . count($events) . " events for user: " . $userId, \OCP\Util::INFO);
            
            return $events;

        } catch (\Exception $e) {
            \OCP\Util::writeLog('activitytimecalculator', "Error in getCalendarEvents for user $userId: " . $e->getMessage(), \OCP\Util::ERROR);
            return [];
        }
    }

    private function getUserPassword(string $userId): ?string {
        // Try to get app-specific password first
        $appPassword = $this->config->getUserValue($userId, 'activitytimecalculator', 'app_password');
        
        if ($appPassword) {
            return $appPassword;
        }

        // For development/demo purposes only - in production, use app passwords
        $user = $this->userManager->get($userId);
        if ($user) {
            // This is just for demo - in real app, you should use app passwords
            return null;
        }

        return null;
    }

    private function fetchEventsViaCalDAV(string $userId, string $password): array {
        // This would be called from the frontend JavaScript
        // For now, return empty array - the actual fetching happens in JavaScript
        return [];
    }

    public function generateTimeReport(string $userId): array {
        try {
            $events = $this->getCalendarEvents($userId);
            
            if (empty($events)) {
                return [
                    'totalTime' => 0,
                    'totalEvents' => 0,
                    'averageDuration' => 0,
                    'eventsByCalendar' => [],
                    'timeByCalendar' => []
                ];
            }

            $totalTime = 0;
            $eventsByCalendar = [];
            $timeByCalendar = [];

            foreach ($events as $event) {
                $duration = $event['duration'] ?? 0;
                $calendar = $event['calendar'] ?? 'Unknown';
                
                $totalTime += $duration;
                
                // Count events by calendar
                if (!isset($eventsByCalendar[$calendar])) {
                    $eventsByCalendar[$calendar] = 0;
                }
                $eventsByCalendar[$calendar]++;
                
                // Sum time by calendar
                if (!isset($timeByCalendar[$calendar])) {
                    $timeByCalendar[$calendar] = 0;
                }
                $timeByCalendar[$calendar] += $duration;
            }

            $totalHours = $totalTime / (1000 * 60 * 60); // Convert ms to hours
            $averageDuration = count($events) > 0 ? $totalTime / count($events) : 0;
            $averageHours = $averageDuration / (1000 * 60 * 60); // Convert ms to hours

            // Convert timeByCalendar to hours
            $timeByCalendarHours = [];
            foreach ($timeByCalendar as $calendar => $time) {
                $timeByCalendarHours[$calendar] = $time / (1000 * 60 * 60);
            }

            return [
                'totalTime' => $totalTime,
                'totalHours' => round($totalHours, 2),
                'totalEvents' => count($events),
                'averageDuration' => $averageDuration,
                'averageHours' => round($averageHours, 2),
                'eventsByCalendar' => $eventsByCalendar,
                'timeByCalendar' => $timeByCalendarHours
            ];

        } catch (\Exception $e) {
            \OCP\Util::writeLog('activitytimecalculator', "Error generating report for user $userId: " . $e->getMessage(), \OCP\Util::ERROR);
            
            return [
                'error' => $e->getMessage(),
                'totalTime' => 0,
                'totalEvents' => 0,
                'averageDuration' => 0
            ];
        }
    }
}
