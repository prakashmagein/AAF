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

namespace Aheadworks\RewardPoints\Model\Data\Processor\Post\Transaction;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Model\Data\Filter\Transaction\CustomerSelection as CustomerSelectionsFilter;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Aheadworks\RewardPoints\Model\Source\Transaction\Status;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Processor
 */
class Processor
{
    /**
     * @var array
     */
    private $filters;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var CustomerSelectionsFilter
     */
    private $customerFilter;

    /**
     * @param ManagerInterface $messageManager
     * @param array $processors
     * @param array $filters
     */
    public function __construct(
        ManagerInterface $messageManager,
        CustomerSelectionsFilter $customerFilter,
        array $processors = [],
        array $filters = []
    ) {
        $this->messageManager = $messageManager;
        $this->filters = $filters;
        $this->processors = $processors;
        $this->customerFilter = $customerFilter;
    }

    /**
     * Prepare post data
     *
     * @param array $data
     * @return array
     */
    public function process(array $data): array
    {
        foreach ($this->processors as $processor) {
            $data = $processor->process($data);
        }
        return $data;
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array|null
     */
    public function filter(array $data): ?array
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filter($data);
        }

        return $this->customerSelectionFilter($data);
    }

    /**
     * Filter customer selection data for create transaction
     *
     * @param array|null $data
     * @return array|null
     */
    public function customerSelectionFilter(?array $data): ?array
    {
        return $this->customerFilter->filter($data);
    }

    /**
     * Validate data
     *
     * @param array $data
     * @param TransactionInterface $transaction
     * @return boolean
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate($data, $transaction)
    {
        $errorNo = true;
        if ($transaction->getStatus() != Status::ACTIVE) {
            $errorNo = false;
            $this->messageManager->addErrorMessage(
                __('You can not save the transaction. The transaction is not active.')
            );
        }
        return $errorNo;
    }

    /**
     * Validate require data
     *
     * @param array $data
     * @return boolean
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'balance' => __('Points balance adjustment'),
            'customer_selections' => __('Customers'),
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields))
                && ((is_array($value) ? count($value) == 0 : $value == ''))) {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in hidden required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }
}
