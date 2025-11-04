<?php
/**
 * @copyright Copyright (c) 2024 Il Tuo Nome
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\ActivityTimeCalculator\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

class Application extends App implements IBootstrap {
    public const APP_ID = 'activitytimecalculator';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Register controllers
        $context->registerController('PageController');
        $context->registerController('ApiController');
    }

    public function boot(IBootContext $context): void {}
}
