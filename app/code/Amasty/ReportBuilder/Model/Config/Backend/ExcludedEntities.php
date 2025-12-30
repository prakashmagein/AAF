<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Config\Backend;

use Amasty\ReportBuilder\Model\Cache\Type as CacheType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider as SchemeProvider;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Exception\LocalizedException;

class ExcludedEntities extends ConfigValue
{
    /**
     * @return ExcludedEntities
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        if ($this->getSchemeProvider() !== null && $this->isValueChanged() && is_array($this->getValue())) {
            $primaryEntities = array_keys(
                $this->getSchemeProvider()->getEntityScheme()->getAllEntitiesOptionArray(true)
            );
            if (!array_diff($primaryEntities, $this->getValue())) {
                throw new LocalizedException(__('Please make sure that at least one Main entity is available.'));
            }
        }

        return parent::beforeSave();
    }

    /**
     * @return ExcludedEntities
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(CacheType::TYPE_IDENTIFIER);
        }

        return parent::afterSave();
    }

    private function getSchemeProvider(): ?SchemeProvider
    {
        return $this->getDataByKey('scheme_provider');
    }
}
