<?php

require_once __DIR__ . '/../common.php';

$config = Om\DiConfig\Config::fromArray([
    'preferences' => [
        [
            'for' => 'MyCalculator',
            'type' => Calculator::class
        ]
    ]
]);
$objectManagerFactory = new \Om\OmFactory($config);
$objectManager = $objectManagerFactory->getInstance();

/** @var Calculator $calculator */
// Even though no class exists called "MyCalculator", still it works because of the above mentioned preference
$calculator = $objectManager->get('MyCalculator');
echo $calculator->add(2, 3);