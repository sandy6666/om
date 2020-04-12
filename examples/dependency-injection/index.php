<?php

require_once __DIR__ . '/../common.php';


$objectManagerFactory = new \Om\OmFactory();
$objectManager = $objectManagerFactory->getInstance();

class Printer {
    public function printMessage($message)
    {
        echo '<br />The Message is : ' . $message . '<br />';
    }
}

class Adder {
    /**
     * @var Printer
     */
    private $printer;

    /**
     * Adder constructor.
     * @param Printer $printer
     */
    public function __construct(
        Printer $printer // injecting object of class Printer in constructor
    )
    {
        $this->printer = $printer;
    }

    public function add($a, $b) {
        $this->printer->printMessage($a + $b);
    }
}

/** @var Adder $adder */
// We don't need to pass the constructor parameters explicitly. It will be passed automatically by the OM
$adder = $objectManager->get(Adder::class);
$adder->add(2, 3);