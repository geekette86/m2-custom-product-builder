<?php
/**
 * cpb
 *
 * NOTICE OF LICENSE
 *
 * Copyright 2016 Profit Soft (http://profit-soft.pro/)
 *
 * Licensed under the Apache License, Version 2.0 (the “License”);
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an “AS IS” BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @package    cpb
 * @author     Denis Kopylov <dv.kopylov@profit-soft.pro>
 * @copyright  Copyright (c) 2016 Profit Soft (http://profit-soft.pro/)
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0 (Apache-2.0)
 */

namespace Buildateam\CustomProductBuilder\Model\Plugin;

class WishlistItem
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    public function __construct(\Magento\Framework\Serialize\Serializer\Json $serializer)
    {
        $this->_serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param callable $proceed
     * @param array $options1
     * @param array $options2
     * @return bool
     */
    public function aroundCompareOptions(\Magento\Wishlist\Model\Item $subject, callable $proceed, $options1, $options2)
    {
        foreach ($options1 as $option) {
            if ($option->getCode() == 'info_buyRequest') {
                $code = $option->getCode();
                $value = @unserialize($option->getValue());
                if ($option->getValue() !== 'b:0;' && $value === false) {
                    $value = $this->_serializer->unserialize($option->getValue());
                }

                if (!isset($value['technicalData'])) {
                    continue;
                }

                $value2 = @unserialize($options2[$code]->getValue())['technicalData'];
                if ($options2[$code]->getValue() !== 'b:0;' && $value2 === false) {
                    $value2 = $this->_serializer->unserialize($options2[$code]->getValue());
                }
                if (!isset($options2[$code]) || $value2['technicalData'] != $value['technicalData']) {
                    return false;
                }
            }
        }
        return $proceed($options1, $options2);
    }
}