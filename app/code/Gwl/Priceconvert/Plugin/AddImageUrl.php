<?php
namespace Gwl\Priceconvert\Plugin;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;

class AddImageUrl
{
    protected $mediaConfig;

    public function __construct(MediaConfig $mediaConfig)
    {
        $this->mediaConfig = $mediaConfig;
    }

    public function afterExportItem($subject, $result, $item)
    {
        if (isset($result['image']) && $result['image']) {
            $baseUrl = $this->mediaConfig->getBaseMediaUrl();
            $result['image'] = $baseUrl . $result['image'];
        }

        return $result;
    }
}
