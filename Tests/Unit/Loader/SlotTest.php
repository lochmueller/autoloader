<?php

namespace HDNET\Autoloader\Tests\Unit\Loader;

use HDNET\Autoloader\Loader\Slots;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * SlotTest
 */
class SlotTest extends UnitTestCase
{
    /**
     * @test
     */
    public function sortByPriority()
    {
        $slots = new Slots();

        $data = [
            ['element' => 1, 'priority' => 50],
            ['element' => 2, 'priority' => 0],
            ['element' => 3, 'priority' => 0],
            ['element' => 4, 'priority' => 100],
            ['element' => 5, 'priority' => 0],
            ['element' => 6, 'priority' => 20],
        ];

        $result = $slots->sortSlotsByPriority($data);

        $expected = [
            ['element' => 4, 'priority' => 100],
            ['element' => 1, 'priority' => 50],
            ['element' => 6, 'priority' => 20],
            ['element' => 2, 'priority' => 0],
            ['element' => 3, 'priority' => 0],
            ['element' => 5, 'priority' => 0],
        ];

        $this->assertSame($expected, $result);
    }
}
