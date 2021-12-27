<?php

declare(strict_types=1);

namespace HDNET\Autoloader\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\FilesProcessor;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * Same as FilesProcessor but for single files.
 */
class FileProcessor extends \FriendsOfTYPO3\Headless\DataProcessing\FilesProcessor
{
    protected array $additionalReferences = [];

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        // Add feature: https://forge.typo3.org/issues/78347
        if (
            isset($processorConfiguration['references.'])
            && $processorConfiguration['references.']
        ) {
            $referencesUidList = (string) $cObj->stdWrapValue('references', $processorConfiguration);
            $this->additionalReferences = GeneralUtility::intExplode(',', $referencesUidList, true);
        }

        $return = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        if (isset($processorConfiguration['as'])) {
            $as = $processorConfiguration['as'];
            if (isset($return[$as]) && \is_array($return[$as]) && 1 === \count($return[$as])) {
                $return[$as] = current($return[$as]);
            }
        }

        return $return;
    }

    protected function fetchData(): array
    {
        $result = parent::fetchData();

        if (!empty($result)) {
            return $result;
        }

        /** @var FileCollector $fileCollector */
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $fileCollector->addFileReferences($this->additionalReferences);

        return $fileCollector->getFiles();
    }
}
