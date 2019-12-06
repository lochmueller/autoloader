<?php

namespace HDNET\Autoloader\Tests\Unit\Loader;

use HDNET\Autoloader\Loader\Slots;
use HDNET\Autoloader\Tests\Unit\AbstractTest;

/**
 * SlotTest.
 */
class SlotTest extends AbstractTest
{
    /**
     * @test
     */
    public function sortByPriority()
    {
        $slots = new Slots();

        $data = [
            50 => [
                ['element' => 1],
            ],
            0 => [
                ['element' => 2],
                ['element' => 3],
                ['element' => 5],
            ],
            100 => [
                ['element' => 4],
            ],
            20 => [
                ['element' => 6],
            ],
        ];

        $result = $slots->flattenSlotsByPriority($data);

        $expected = [
            ['element' => 4],
            ['element' => 1],
            ['element' => 6],
            ['element' => 2],
            ['element' => 3],
            ['element' => 5],
        ];

        $this->assertSame($expected, $result);
    }
}
