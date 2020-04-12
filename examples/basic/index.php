<?php

require_once __DIR__ . '/../common.php';


$objectManagerFactory = new \Om\OmFactory();
$objectManager = $objectManagerFactory->getInstance();

/** @var Calculator $calculator */
$calculator = $objectManager->get(Calculator::class);
echo $calculator->add(2, 3);