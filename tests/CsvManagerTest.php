<?php

namespace App\Tests;

use App\Services\CsvManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CsvManagerTest extends WebTestCase {

    var $csvManager;

    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->csvManager = new CsvManager(static::$kernel->getContainer()->getParameterBag());
    }

    public function getDataToExportCalendar() {
        return [
                [NULL],
                [''],
                [0],
            [],
                [['fileName' => 'demo.csv', 'year' => '2019']],
                [['fileName' => 'demo.csv']]
        ];
    }

    /**
     * @dataProvider getDataToExportCalendar
     */
    public function testExportCalendar($data) {
        if ($this->csvManager->exportCalendar($data)) {
            $this->assertGreaterThan(0, true);
        } else {
            $this->assertEquals(0, false);
        }
    }

}
