<?php
/**
 * @author Elsner Team
 * @copyright Copyright © Elsner Technologies Pvt. Ltd (https://www.elsner.com/)
 * @package Elsnertech_Paytrace
 */

namespace Elsnertech\Paytrace\Block\Info;

use Magento\Payment\Block\Info\Cc;

class Paytrace extends Cc
{
    
    /**
     * Get transection detail
     *
     * @return array
     */
    public function getTransectionDetail()
    {
        $data = $this->getMethod()->fetchTransactionDetailInfo(
            $this->getInfo(),
            $this->getInfo()->getLastTransId()
        );

        $detailData = [];
        if (isset($data['success']) && $data['success'] === true) {
            if (isset($data['transaction_id'])) {
                $detailData['transaction_id'] = ['label'=>__('Transaction Id'),
                    'value'=>$data['transaction_id']
                ];
            }
            if (isset($data['approval_code'])) {
                $detailData['approval_code'] = ['label'=>__('Approval Code'),
                    'value'=>$data['approval_code']
                ];
            }
            if (isset($data['response_code'])) {
                $detailData['response_code'] = ['label'=>__('Response Code'),
                    'value'=>$data['response_code']
                ];
            }
            if (isset($data['status_message'])) {
                $detailData['status_message'] = ['label'=>__('Status Message'),
                    'value'=>$data['status_message']
                ];
            }
        }
        return $detailData;
    }

    /**
     * Get transection information
     *
     * @param Transport $transport
     * @return object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $transectionDetail = $this->getTransectionDetail();

        if (empty($transectionDetail)  !== true) {
            foreach ($transectionDetail as $key => $value) {
               
                $data[(string)$value['label']] = $value['value'];
            }
        }
        return $transport->setData(
            array_merge($transport->getData(), $data)
        );
    }
}
