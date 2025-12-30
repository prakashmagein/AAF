<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

class SuperUsers implements ArrayInterface
{
    /**
     * const ADMIN_ROLE
     */
    const ADMIN_ROLE = "Administrators";

    protected $optionArray;

    /**
     * @var UserCollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        if (!isset($this->optionArray)) {
            $this->optionArray = [];

            $superUsers = $this->userCollectionFactory->create();

            $superUsers->getSelect()
                ->where("`detail_role`.`role_name` = '".(self::ADMIN_ROLE).
                    "' AND user_role.parent_id=detail_role.role_id");

            foreach ($superUsers as $superUser) {
                $this->optionArray[] = ['value' => $superUser->getId(), 'label' => $superUser->getUserName()];
            }
        }

        return $this->optionArray;
    }
}
