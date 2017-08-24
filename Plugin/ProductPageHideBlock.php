<?php

namespace Buildateam\CustomProductBuilder\Plugin;


class ProductPageHideBlock
{
    /**
     * @param \Magento\ProductVideo\Block\Product\View\Gallery|Magento\Catalog\Block\Product\View\Gallery $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundToHtml($subject, callable $proceed)
    {

        $hasConfig = (bool)$subject->getProduct()->getData('json_configuration');
        if ($hasConfig) return "";
        
        return $proceed();
    }

}