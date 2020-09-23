<?php
namespace Sparsh\CustomOrderNumber\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Data
 * @package Sparsh\CustomOrderNumber\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Data constructor.
     *
     * @param Context
     * @param ConfigInterface $configInterface
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     */
    public function __construct(
        Context $context,
        ConfigInterface $configInterface,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->configInterface = $configInterface;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * Retrieve config value
     *
     * @param $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue($config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve custom order number for order number, invoice number, shipment number, credit memo number
     *
     * @param $incrementId
     * @param $configurationType
     * @return mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomNumber($incrementId, $configurationType)
    {
        $numberFormat = $this->getConfig('sparsh_customordernumber/'.$configurationType.'/'.$configurationType.'_number_format');

        if (strpos($numberFormat, '{d}') !== false) {
            $numberFormat = str_replace('{d}', date('j'), $numberFormat);
        }
        if (strpos($numberFormat, '{dd}') !== false) {
            $numberFormat = str_replace('{dd}', date('d'), $numberFormat);
        }
        if (strpos($numberFormat, '{m}') !== false) {
            $numberFormat = str_replace('{m}', date('n'), $numberFormat);
        }
        if (strpos($numberFormat, '{mm}') !== false) {
            $numberFormat = str_replace('{mm}', date('m'), $numberFormat);
        }
        if (strpos($numberFormat, '{yy}') !== false) {
            $numberFormat = str_replace('{yy}', date('y'), $numberFormat);
        }
        if (strpos($numberFormat, '{yyyy}') !== false) {
            $numberFormat = str_replace('{yyyy}', date('Y'), $numberFormat);
        }
        if (strpos($numberFormat, '{store_id}') !== false) {
            $storeId = $this->storeManager->getStore()->getId();
            $numberFormat = str_replace('{store_id}', $storeId, $numberFormat);
        }
        if (strpos($numberFormat, '{counter}') !== false) {
            $counterStartUp = $this->getConfig('sparsh_customordernumber/'.$configurationType.'/'.$configurationType.'_counter_start_from');
            $counterStartUp = $counterStartUp ?  $counterStartUp : 1;

            $incrementNumber = $this->getConfig('sparsh_customordernumber/'.$configurationType.'/'.$configurationType.'_counter_increment_by');
            $incrementNumber = $incrementNumber ? $incrementNumber : 1;

            $counterLength = $this->getConfig('sparsh_customordernumber/'.$configurationType.'/'.$configurationType.'_counter_number_padding');
            $counterLength  = $counterLength ? $counterLength  : 0;

            $configConnection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('core_config_data');
            $path = 'sparsh_customordernumber/'.$configurationType.'/'.$configurationType.'_counter';
            $selectData = $configConnection->select()->from($tableName)->where('path=?', $path);
            $configData = $configConnection->fetchRow($selectData);
            $counter = isset($configData['value'])?$configData['value']:null;
            if ($counter === null || $counterStartUp > $counter) {
                $counter = $counterStartUp;
            } else {
                $counter += $incrementNumber;
            }
            $this->configInterface->saveConfig($path, $counter, 'default', 0);
            $paddedCounter = str_pad($counter, $counterLength, '0', STR_PAD_LEFT);
            $numberFormat = str_replace('{counter}', $paddedCounter, $numberFormat);
            return $numberFormat;
        }
        return $numberFormat.$incrementId;
    }
}
