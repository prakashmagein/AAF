<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\Data\Filter\Transaction;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Magento\Customer\Model\Config\Share;

/**
 * Class CustomerSelection
 */
class CustomerSelection implements FilterInterface
{
    /**#@+
     * Constant for default field name for customer selection
     */
    const DEFAULT_FIELD_NAME = 'customer_selections';
    /**#@-*/

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var Share
     */
    private $configShare;

    /**
     * @param string $fieldName
     * @param Share $configShare
     */
    public function __construct(
        Share $configShare,
        $fieldName = null
    ) {
        if ($fieldName == null) {
            $this->fieldName = self::DEFAULT_FIELD_NAME;
        } else {
            $this->fieldName = $fieldName;
        }
        $this->configShare = $configShare;
    }

    /**
     *  Filter customer selection data for create transaction
     *
     * @param array $data
     * @return array|null
     */
    public function filter(array $data): ?array
    {
        $result = [];
        if (is_array($data)
            && isset($data[$this->fieldName])
            && is_array($data[$this->fieldName])
        ) {
            foreach ($data[$this->fieldName] as $customerSelection) {
                if ($this->isCustomerDataValid($data, $customerSelection)) {
                    $result[] = [
                        TransactionInterface::CUSTOMER_ID => $this->get(
                            $customerSelection,
                            TransactionInterface::CUSTOMER_ID
                        ),
                        TransactionInterface::CUSTOMER_NAME => $this->get(
                            $customerSelection,
                            TransactionInterface::CUSTOMER_NAME
                        ),
                        TransactionInterface::CUSTOMER_EMAIL => $this->get(
                            $customerSelection,
                            TransactionInterface::CUSTOMER_EMAIL
                        ),
                        TransactionInterface::COMMENT_TO_CUSTOMER => $this->get(
                            $data,
                            TransactionInterface::COMMENT_TO_CUSTOMER
                        ),
                        TransactionInterface::COMMENT_TO_ADMIN => $this->get(
                            $data,
                            TransactionInterface::COMMENT_TO_ADMIN
                        ),
                        TransactionInterface::BALANCE => $this->get(
                            $data,
                            TransactionInterface::BALANCE
                        ),
                        TransactionInterface::EXPIRATION_DATE => $this->get(
                            $data,
                            TransactionInterface::EXPIRATION_DATE
                        ),
                        TransactionInterface::WEBSITE_ID => $this->get(
                            $data,
                            TransactionInterface::WEBSITE_ID
                        ),

                    ];
                }
            }
        }
        return $result;
    }

    /**
     * Checks is need to add customer selection data to result
     *
     * @param array $data
     * @param array $customerData
     * @return bool
     */
    private function isCustomerDataValid($data, $customerData)
    {
        $result = true;
        $websiteId = $this->get($data, TransactionInterface::WEBSITE_ID);
        $customerWebsiteId = $this->get($customerData, TransactionInterface::WEBSITE_ID);
        if (is_array($customerWebsiteId)) {
            $customerWebsiteId = $customerWebsiteId[0];
        }
        if ($this->configShare->isWebsiteScope() && $customerWebsiteId != $websiteId) {
            $result = false;
        }
        return $result;
    }

    /**
     * Get data from array
     *
     * @param array $data
     * @param string $field
     * @return string
     */
    private function get($data, $field)
    {
        return (is_array($data) && isset($data[$field])) ? $data[$field] : null;
    }
}
