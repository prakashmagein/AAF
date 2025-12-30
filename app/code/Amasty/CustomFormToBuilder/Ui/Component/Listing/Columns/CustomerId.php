<?php

declare(strict_types=1);

namespace Amasty\CustomFormToBuilder\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class CustomerId extends Column
{
    const GUEST_ID = 0;

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName]) && $item[$fieldName] == self::GUEST_ID) {
                    $item[$fieldName] = __('Guest');
                }
            }
        }

        return $dataSource;
    }
}
