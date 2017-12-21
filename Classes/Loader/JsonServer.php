<?php

/**
 * Loading JsonServer.
 */
declare(strict_types=1);

namespace HDNET\Autoloader\Loader;

use HDNET\Autoloader\Loader;
use HDNET\Autoloader\Utility\ClassNamingUtility;
use HDNET\Autoloader\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Loading JsonServer.
 */
class JsonServer extends AbstractServerLoader
{
    /**
     * Server name.
     *
     * @var string
     */
    protected $serverName = 'Json';

    /**
     * Get all the complex data for the loader.
     * This return value will be cached and stored in the database
     * There is no file monitoring for this cache.
     *
     * @param Loader $autoLoader
     * @param int    $type
     *
     * @return array
     */
    public function prepareLoader(Loader $autoLoader, $type)
    {
        $servicePath = ExtensionManagementUtility::extPath($autoLoader->getExtensionKey()) . 'Classes/Service/Json/';
        $serviceClasses = FileUtility::getBaseFilesRecursivelyInDir($servicePath, 'php');

        $info = [];
        foreach ($serviceClasses as $service) {
            $serviceClass = ClassNamingUtility::getFqnByPath(
                $autoLoader->getVendorName(),
                $autoLoader->getExtensionKey(),
                'Service/Json/' . $service
            );

            $legacyServiceName = \lcfirst($service);
            if (\array_key_exists($legacyServiceName, $info)) {
                \trigger_error('Service "' . $service . '" already defined in: ' . $info[$legacyServiceName] . '!"', E_USER_NOTICE);
            }
            $info[$legacyServiceName] = $serviceClass;

            $serviceName = $autoLoader->getExtensionKey() . '/' . $service;
            $info[$serviceName] = $serviceClass;
        }

        return $info;
    }
}
