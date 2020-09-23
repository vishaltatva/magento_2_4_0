<?php
namespace Sparsh\CustomOrderNumber\Plugin\Sales\Model;

/**
 * Class Order
 * @package Sparsh\CustomOrderNumber\Plugin\Sales\Model
 */
class Order
{
    /**
     * @var \Sparsh\CustomOrderNumber\Helper\Data
     */
    private $customOrderNumberHelper;

    /**
     * Order constructor.
     *
     * @param \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper
     */
    public function __construct(
        \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper
    ) {
        $this->customOrderNumberHelper = $customOrderNumberHelper;
    }

    /**
     * Place order
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param $result
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterPlace(\Magento\Sales\Model\Order $subject, $result)
    {
        $isModuleEnabled = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/general/enable_customordernumber');
        $isCustomOrderNumberEnabled  = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/order/enable_customordernumber_order');
        $orderNumberFormat = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/order/order_number_format');
        if ($isModuleEnabled && $isCustomOrderNumberEnabled && $orderNumberFormat) {
            $customOrderId = $this->customOrderNumberHelper->getCustomNumber($subject->getIncrementId(), 'order');
            if ($customOrderId) {
                $subject->setIncrementId($customOrderId)->save();
            }
        }
        return $result;
    }
}
