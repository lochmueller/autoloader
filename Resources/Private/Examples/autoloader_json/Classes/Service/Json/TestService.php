<?php
/**
 * Test service
 *
 * @author  Tim Lochmüller
 * @author  Tito Duarte
 */

namespace HDNET\AutoloaderSoap\Service\Json;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Test service
 */
class TestService implements SingletonInterface
{

    /**
     * Test function
     *
     * @param string $test
     *
     * @return int
     */
    public function testFunction($test)
    {
        return 1;
    }
}
