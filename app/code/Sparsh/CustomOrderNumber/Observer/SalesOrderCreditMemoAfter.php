<?php
namespace Sparsh\CustomOrderNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderCreditMemoAfter
 * @package Sparsh\CustomOrderNumber\Observer
 */
class SalesOrderCreditMemoAfter implements ObserverInterface
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
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * SalesOrderCreditMemoAfter constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Sparsh\CustomOrderNumber\Helper\Data $customOrderNumberHelper,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customOrderNumberHelper = $customOrderNumberHelper;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->searchCriteria =  $searchCriteria;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isModuleEnabled = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/general/enable_customordernumber');
        $isCustomCreditmemoNumberEnabled  = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/creditmemo/enable_customordernumber_creditmemo');
        if ($isModuleEnabled && $isCustomCreditmemoNumberEnabled) {
            $creditmemo = $this->coreRegistry->registry('current_creditmemo');
            $isCreditmemoSameAsOrder = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/creditmemo/creditmemo_same_as_order_number');
            if ($isCreditmemoSameAsOrder) {
                $orderIncrementId = $creditmemo->getOrder()->getIncrementId();
                $orderPart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/creditmemo/creditmemo_order_part');
                $creditmemoPart = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/creditmemo/creditmemo_part');
                $customCreditmemoId = $orderIncrementId;
                if ($orderPart && $creditmemoPart) {
                    if (strpos($orderIncrementId, $orderPart) !== false) {
                        $customCreditmemoId = str_replace($orderPart, $creditmemoPart, $orderIncrementId);
                    }
                }
            } else {
                $creditmemoNumberFormat = $this->customOrderNumberHelper->getConfig('sparsh_customordernumber/creditmemo/creditmemo_number_format');
                if ($creditmemoNumberFormat) {
                    $customCreditmemoId = $this->customOrderNumberHelper->getCustomNumber($creditmemo->getIncrementId(), 'creditmemo');
                }
            }
            if ($customCreditmemoId) {
                $creditmemoRepositoryCollection  = $this->creditmemoRepository->getList($this->searchCriteria);
                $creditmemos = $creditmemoRepositoryCollection->getItems();
                $count = 0;
                foreach ($creditmemos as $creditmemo) {
                    if ((strpos($creditmemo->getIncrementId(), $customCreditmemoId) !== false)) {
                        $count++;
                    }
                }
                if ($count) {
                    $creditmemo->setIncrementId($customCreditmemoId.'-'.$count)->save();
                } else {
                    $creditmemo->setIncrementId($customCreditmemoId)->save();
                }
            }
        }
    }
}
