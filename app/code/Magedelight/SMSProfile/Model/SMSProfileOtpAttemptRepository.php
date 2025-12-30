<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Model;

use Magedelight\SMSProfile\Api\SMSProfileOtpAttemptRepositoryInterface;
use Magedelight\SMSProfile\Api\Data\SMSProfileOtpAttemptInterface;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\Collection;
use Magedelight\SMSProfile\Model\SMSProfileOtpAttemptFactory;
use Magedelight\SMSProfile\Model\SMSProfileOtpAttempt;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt as ResourceModelSmsProfileTemaplate;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SMSProfileOtpAttemptRepository implements SMSProfileOtpAttemptRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SMSProfileOtpAttemptFactory
     */
    private $smsProfileOtpAttemptFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\smsProfileOtpAttempt
     */
    private $resourceModel;

    /**
     * SMSTemplatesRepository constructor.
     * @param SMSProfileOtpAttemptFactory $smsProfileOtpAttempt
     * @param CollectionFactory $collectionFactory
     * @param ResourceModelSmsProfileTemaplate $resourceModel
     */
    
    public function __construct(
        ResourceModelSmsProfileTemaplate $resourceModel,
        SMSProfileOtpAttemptFactory $smsProfileOtpAttempt,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->smsProfileOtpAttemptFactory = $smsProfileOtpAttempt;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById($id)
    {
        $smsProfileOtpAttempt = $this->smsProfileOtpAttemptFactory->create();
        $this->resourceModel->load($smsProfileOtpAttempt, $id);
        if (!$smsProfileOtpAttempt->getId()) {
            throw new NoSuchEntityException(__('smsProfileOtpAttempt with id "%1" does not exist.', $id));
        }
        return $smsProfileOtpAttempt;
    }

    public function save(SMSProfileOtpAttemptInterface $smsProfileOtpAttempt)
    {
        try {
            $this->resourceModel->save($smsProfileOtpAttempt);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $smsProfileOtpAttempt;
    }

    public function delete(SMSProfileOtpAttemptInterface $smsProfileOtpAttempt)
    {
        try {
            $this->resourceModel->delete($smsProfileOtpAttempt);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }
}
