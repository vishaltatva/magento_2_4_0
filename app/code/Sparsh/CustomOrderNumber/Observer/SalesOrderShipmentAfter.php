<?php
namespace Sparsh\CustomOrderNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderShipmentAfter
 * @package Sparsh\CustomOrderNumber\Observer
 */
class SalesOrderShipmentAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Sparsh\CustomOrderNumber\Helper\Data
     */
    private $customOrderNumberHelper;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * SalesOrderShipmentAfter constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customOrderNumberHelper = $customOrderNumberHelper;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteria =  $searchCriteria;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isModuleEnabled = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/general/enable_customordernumber');
        $isCustomShipmentNumberEnabled  = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/enable_customordernumber_shipment');
        if ($isModuleEnabled && $isCustomShipmentNumberEnabled) {
            $shipment = $this->coreRegistry->registry('current_shipment');
            $isShipmentSameAsOrder = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/shipment_same_as_order_number');
            if ($isShipmentSameAsOrder) {
                $orderIncrementId = $shipment->getOrder()->getIncrementId();
                $orderPart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/shipment_order_part');
                $shipmentPart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/shipment_part');
                $customShipmentId = $orderIncrementId;
                if ($orderPart && $shipmentPart) {
                    if (strpos($orderIncrementId, $orderPart) !== false) {
                        $customShipmentId = str_replace($orderPart, $shipmentPart, $orderIncrementId);
                    }
                }
            } else {
                $shipmentNumberFormat = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/shipment_number_format');
                if ($shipmentNumberFormat) {
                    $customShipmentId = $this->customOrderNumberHelper->getCustomNumber($shipment->getIncrementId(), 'shipment');
                }
            }
            if ($customShipmentId) {
                $shipmentRepositoryCollection  = $this->shipmentRepository->getList($this->searchCriteria);
                $shipments = $shipmentRepositoryCollection->getItems();
                $count = 0;
                foreach ($shipments as $shipment) {
                    if ((strpos($shipment->getIncrementId(), $customShipmentId) !== false)) {
                        $count++;
                    }
                }
                if ($count) {
                    $shipment->setIncrementId($customShipmentId.'-'.$count)->save();
                } else {
                    $shipment->setIncrementId($customShipmentId)->save();
                }
            }
        }
    }
}
