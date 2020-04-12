<?php

require_once __DIR__ . '/../common.php';


$objectManagerFactory = new \Om\OmFactory();
$objectManager = $objectManagerFactory->getInstance();

class Sum {
    private $sum = 0;

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @param mixed $sum
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }
}


/**
 * $objectManager::get() returns a singleton instance by default
 * i.e, if object already created, it returns the same instance
 * or creates new instance
 */

/** @var Sum $sum */
$sum = $objectManager->get(Sum::class);
$sum->setSum(0);


/** @var Calculator $calculator */
$calculator = $objectManager->get(Calculator::class);
for ($i = 0; $i < 10; $i++) {
    /** @var Sum $sum */
    $sum = $objectManager->get(Sum::class);
    $sum->setSum($calculator->add($sum->getSum(), $i));
}

/** @var Sum $sum */
$sum = $objectManager->get(Sum::class);
echo 'With singleton ' . $sum->getSum(); // 45

echo '<br /><br />';
//--------------------------------------------------------------------------------


/**
 * $objectManager::create() returns a new instance of the object every time
 */

/** @var Sum $sum */
$sum = $objectManager->create(Sum::class);
$sum->setSum(0);


/** @var Calculator $calculator */
$calculator = $objectManager->get(Calculator::class);
for ($i = 0; $i < 10; $i++) {
    /** @var Sum $sum */
    $sum = $objectManager->create(Sum::class);
    $sum->setSum($calculator->add($sum->getSum(), $i));
}

/** @var Sum $sum */
$sum = $objectManager->create(Sum::class);
echo 'Without singleton ' . $sum->getSum(); // 0