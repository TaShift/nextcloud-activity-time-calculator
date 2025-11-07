<?php

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
        // Registra i file statici
        $context->registerService('JSFileService', function($c) {
            return new \OCP\AppFramework\Services\InitialStateProvider(
                $c->query('ServerContainer')->getURLGenerator()
            );
        });
    }

    public function boot(IBootContext $context): void {
    }
}
