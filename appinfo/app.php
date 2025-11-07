<?php

declare(strict_types=1);

namespace OCA\ActivityTimeCalculator\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
    public const APP_ID = 'activitytimecalculator';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Register controllers
        $context->registerService('ApiController', function($c) {
            return new \OCA\ActivityTimeCalculator\Controller\ApiController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('UserSession'),
                $c->query('ReportService')
            );
        });
        
        // Register services
        $context->registerService('ReportService', function($c) {
            return new \OCA\ActivityTimeCalculator\Service\ReportService(
                $c->query('Config'),
                $c->query('UserManager'),
                $c->query('CalendarManager')
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Boot logic if needed
    }
}
