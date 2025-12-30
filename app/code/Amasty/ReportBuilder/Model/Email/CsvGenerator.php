<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Email;

use Amasty\ReportBuilder\Ui\Export\ToCsv;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;

class CsvGenerator
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var ToCsv
     */
    private $toCsv;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        Filesystem $filesystem,
        ToCsv $toCsv,
        State $appState
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->toCsv = $toCsv;
        $this->appState = $appState;
    }

    public function getCsvContent(int $reportId): string
    {
        $file = $this->createCsvFile($reportId);

        $content = $this->directory->readFile($file);
        $this->directory->delete($file);

        return $content;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function createCsvFile(int $reportId): string
    {
        $csvData = $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this->toCsv, 'getCsvFile']
        ); // emulate for correct reading ui component in adminhtml area

        return $csvData['value'];
    }
}
