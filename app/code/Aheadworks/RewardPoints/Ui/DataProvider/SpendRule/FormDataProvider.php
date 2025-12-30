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

namespace Aheadworks\RewardPoints\Ui\DataProvider\SpendRule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface as DataProcessorInterface;
use Aheadworks\RewardPoints\Model\Meta\ProcessorInterface as MetaProcessorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class FormDataProvider
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_reward_points_spend_rule';

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectProcessor $dataObjectProcessor
     * @param SpendRuleRepositoryInterface $ruleRepository
     * @param DataProcessorInterface $dataProcessor
     * @param MetaProcessorInterface $metaProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        private RequestInterface $request,
        private DataPersistorInterface $dataPersistor,
        private DataObjectProcessor $dataObjectProcessor,
        private SpendRuleRepositoryInterface $ruleRepository,
        private DataProcessorInterface $dataProcessor,
        private MetaProcessorInterface $metaProcessor,
        private Logger $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);
        $id = $this->request->getParam($this->getRequestFieldName());

        if (!empty($dataFromForm)) {
            $preparedData = $dataFromForm;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
        } elseif (!empty($id)) {
            try {
                $rule = $this->ruleRepository->get($id);
                $data = $this->dataObjectProcessor->buildOutputDataArray($rule, SpendRuleInterface::class);
                $preparedData[$id] = $this->dataProcessor->process($data);
            } catch (NoSuchEntityException $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }

        return $preparedData;
    }

    /**
     * Return Meta
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->metaProcessor->process($meta, $this->getData());
        return $meta;
    }

    /**
     * Add field filter to collection
     *
     * @param Filter $filter
     * @return mixed
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }
}
