<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;

class ToCsv
{
    public const DELIMITER = ',';

    /**
     * @var DirectoryList
     */
    private $directory;

    /**
     * @var MetadataProvider
     */
    private $metadataProvider;

    /**
     * @var int|null
     */
    private $pageSize = null;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var array
     */
    private $fieldsOptions = [];

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        $pageSize = 5000
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = uniqid($component->getName(), true);
        $file = 'export/' . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        /** @var \Amasty\ReportBuilder\Ui\DataProvider\Listing\View\DataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();
        $this->prepareFieldsOptions($component);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $fields = $this->metadataProvider->getFields($component);
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $collection = $dataProvider->getCollection();
        $collection->setCurPage($i)->setPageSize($this->pageSize);
        $totalCount = (int) $collection->getSize();
        while ($totalCount > 0) {
            foreach ($collection->loadData() as $itemData) {
                $row = [];
                foreach ($fields as $field) {
                    $row[$field] = ((null !== $itemData[$field]) && isset($this->fieldsOptions[$field]))
                        ? $this->prepareValue($field, $itemData[$field])
                        : $itemData[$field];
                }

                $stream->writeCsv($row);
            }
            $collection->setCurPage(++$i);
            $collection->reset();
            $totalCount -= $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    private function prepareFieldsOptions(UiComponentInterface $component): void
    {
        $columnsComponent = $this->getColumnsComponent($component)->getChildComponents();

        foreach ($columnsComponent as $key => $column) {
            $options = $column->getConfiguration()['options'] ?? null;
            if (!$options) {
                continue;
            }

            $this->fillOptions($key, $options);
        }
    }

    /**
     * Support multidimensional $options.
     */
    private function fillOptions(string $key, array $options): void
    {
        foreach ($options as $option) {
            if (empty($option['value']) || empty($option['label'])) {
                continue;
            }

            if (is_array($option['value'])) {
                $this->fillOptions($key, $option['value']);
                continue;
            }

            $this->fieldsOptions[$key][$option['value']] = $option['label'];
        }
    }

    private function getColumnsComponent(UiComponentInterface $component): ?UiComponentInterface
    {
        $columnsComponent = $component->getComponent('view_columns');

        if (!$columnsComponent) {
            foreach ($component->getChildComponents() as $childComponent) {
                if ($childComponent instanceof Columns) {
                    $columnsComponent = $childComponent;
                }
            }
        }

        return $columnsComponent ?? null;
    }

    private function prepareValue(string $field, string $fieldValue): string
    {
        if (!empty($this->fieldsOptions[$field])) {
            $options = $this->getOptionsIdsAsArrayKeys($fieldValue);
            $fieldValue = implode(
                self::DELIMITER . ' ',
                array_intersect_key($this->fieldsOptions[$field], $options)
            );
        }

        return $fieldValue;
    }

    private function getOptionsIdsAsArrayKeys(string $fieldValue): array
    {
        $options = explode(self::DELIMITER, $fieldValue);
        $options = array_map('trim', $options);

        return array_flip($options);
    }
}
