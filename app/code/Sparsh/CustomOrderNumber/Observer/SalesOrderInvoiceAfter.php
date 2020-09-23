<?php
namespace Sparsh\CustomOrderNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderInvoiceAfter
 * @package Sparsh\CustomOrderNumber\Observer
 */
class SalesOrderInvoiceAfter implements ObserverInterface
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
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * SalesOrderInvoiceAfter constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentFactory $shipmentFactory
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customOrderNumberHelper = $customOrderNumberHelper;
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteria =  $searchCriteria;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isModuleEnabled = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/general/enable_customordernumber');
        $isCustomInvoiceNumberEnabled  = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/invoice/enable_customordernumber_invoice');

        $invoice = $this->coreRegistry->registry('current_invoice');

        if ($isModuleEnabled && $isCustomInvoiceNumberEnabled) {

            $isInvoiceSameAsOrder = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/invoice/invoice_same_as_order_number');
            if ($isInvoiceSameAsOrder) {
                $orderIncrementId = $invoice->getOrder()->getIncrementId();
                $orderPart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/invoice/invoice_order_part');
                $invoicePart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/invoice/invoice_part');
                $customInvoiceId = $orderIncrementId;
                if ($orderPart && $invoicePart) {
                    if (strpos($orderIncrementId, $orderPart) !== false) {
                        $customInvoiceId = str_replace($orderPart, $invoicePart, $orderIncrementId);
                    }
                }
            } else {
                $invoiceNumberFormat = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/invoice/invoice_number_format');
                if ($invoiceNumberFormat) {
                    $customInvoiceId = $this->customOrderNumberHelper->getCustomNumber($invoice->getIncrementId(), 'invoice');
                }
            }
            if ($customInvoiceId) {
                $invoiceRepositoryCollection  = $this->invoiceRepository->getList($this->searchCriteria);
                $invoices = $invoiceRepositoryCollection->getItems();
                $count = 0;
                foreach ($invoices as $invoice) {
                    if ((strpos($invoice->getIncrementId(), $customInvoiceId) !== false)) {
                        $count++;
                    }
                }
                if ($count) {
                    $invoice->setIncrementId($customInvoiceId.'-'.$count)->save();
                } else {
                    $invoice->setIncrementId($customInvoiceId)->save();
                }
            }
        }

        //Change shipment number to custom shipment number if shipment was created with invocie
        $isCustomShipmentNumberEnabled  = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/shipment/enable_customordernumber_shipment');

        if ($isModuleEnabled && $isCustomShipmentNumberEnabled) {
            if( $invoice ){
                $order = $invoice->getOrder();

                if($order){
                    $shipmentCollection = $order->getShipmentsCollection();
                    foreach($shipmentCollection as $shipmentData){
                        $shipment = $shipmentData;
                    }
                }
            }

            if(isset($shipment) && $shipment){
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
}
