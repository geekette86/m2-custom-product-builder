<?php
/**
 * Copyright (c) 2017 Indigo Geeks, Inc. All rights reserved.
 *
 * General.
 * The custom product builder software and documentation accompanying this License
 * whether on disk, in read only memory, on any other media or in any other form (collectively
 * the “Software”) are licensed, not sold, to you by copyright holder, Indigo Geeks, Inc.
 * (“Buildateam”) for use only under the terms of this License, and Buildateam reserves all rights
 * not expressly granted to you. The rights granted herein are limited to Buildateam’s intellectual
 * property rights in the Buildateam Software and do not include any other patents or
 * intellectual property rights. You own the media on which the Buildateam Software is
 * recorded but Buildateam and/or Buildateam’s licensor(s) retain ownership of the Software
 * itself.
 *
 * Permitted License Uses and Restrictions.
 * This License allows you to install and use one (1) copy of the Software.
 * This License does not allow the Software to exist on more than one production domain.
 * Except as and only to the extent expressly permitted in this License or by applicable
 * law, you may not copy, decompile, reverse engineer, disassemble, attempt to derive
 * the source code of, modify, or create derivative works of the Software or any part
 * thereof. Any attempt to do so is a violation of the rights of Buildateam and its licensors of
 * the Software. If you breach this restriction, you may be subject to prosecution and
 * damages.
 *
 * Transfer.
 * You may not rent, lease, lend or sublicense the Software.
 *
 * Termination.
 * This License is effective until terminated. Your rights under this
 * License will terminate automatically without notice from Buildateam if you fail to comply
 * with any term(s) of this License. Upon the termination of this License, you shall cease
 * all use of the Buildateam Software and destroy all copies, full or partial, of the Buildateam
 * Software.
 *
 * THIS SOFTWARE IS PROVIDED BY COPYRIGHT HOLDER "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL COPYRIGHT HOLDER BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. THE SOFTWARE IS NOT INTENDED FOR USE IN WHICH THE FAILURE OF
 * THE SOFTWARE COULD LEAD TO DEATH, PERSONAL INJURY, OR SEVERE PHYSICAL OR ENVIRONMENTAL DAMAGE.
 */

namespace Buildateam\CustomProductBuilder\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Logger\Monolog;
use Exception;

class ImportFile extends \Magento\Backend\App\Action
{

    protected $_jsonProductContent;

    /**
     * @var ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var Monolog
     */
    protected $_logger;

    /**
     * Share constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Monolog $logger
    )
    {
        $this->_resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productId = $this->getRequest()->getParam('id');
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);

        $file = $this->getRequest()->getFiles('product')['file'];
        $jsonData = !empty($file['tmp_name'])
            ? file_get_contents($file['tmp_name'])
            : $product->getData('json_configuration');

        $response = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        if (!empty($jsonData)) {
            $this->_jsonProductContent = $jsonData;
            $validate = $this->_objectManager->create('Buildateam\CustomProductBuilder\Helper\Data')->validate($this->_jsonProductContent);

            if (isset($this->_jsonProductContent) && !empty($this->_jsonProductContent) && $validate) {
                $result = [
                    'status' => 'error',
                    'msg' => $validate
                ];

                $response->setData($result);
                return $response;
            }

            $product->setJsonConfiguration($this->_jsonProductContent);

            try {
                $product->save();
            } catch (Exception $e) {
                $this->_logger->critical($e->getMessage());
            }

            $result = [
                'status' => 'success',
                'msg' => 'Custom Product Builder imported with success!'
            ];

            $response->setData($result);
        }

        return $response;
    }
}