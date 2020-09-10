<?php
/**
 * Sparsh Push Notification Module
 *
 * php version 7
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\PushNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    const XML_PATH_ENABLEPUSHNOTIFICATION='pushnotification/generalpushnotification/enablenotification';
    const XML_PATH_APIKEY = 'pushnotification/generalpushnotification/apikey';
    const XML_PATH_AUTHDOMAIN='pushnotification/generalpushnotification/authdomain';
    const XML_PATH_DATABASEURL='pushnotification/generalpushnotification/databaseurl';
    const XML_PATH_PROJECTID='pushnotification/generalpushnotification/projectid';
    const XML_PATH_STORAGEBUCKET='pushnotification/generalpushnotification/storagebucket';
    const XML_PATH_MESSAGESENDERID='pushnotification/generalpushnotification/messagingsenderid';

    const XML_PATH_SERVER_KEY ='pushnotification/generalpushnotification/serverkey';
    const XML_PATH_PROMPT_ENABLE='pushnotification/prompt/enable';
    const XML_PATH_PROMPT_TEXT='pushnotification/prompt/prompt_text';
    const XML_PATH_PROMPT_SHOW_DELAY='pushnotification/prompt/show_delay';
    const XML_PATH_PROMPT_FREQUENCY='pushnotification/prompt/frequency';
    const XML_PATH_PROMPT_PAGES='pushnotification/prompt/pages';
    const XML_PATH_PROMPT_PAGE_URL='pushnotification/prompt/page_url';
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
        parent::__construct($context);
    }
    /**
     * Get ConfigValue
     *
     * @param String $field   field
     * @param null   $storeId storeId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Server Key
     *
     * @return mixed
     */
    public function getServerKey()
    {
        return $this->getConfigValue(self::XML_PATH_SERVER_KEY);
    }
    /**
     * Get Media Url
     *
     * @return mixed
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
    /**
     * Get Base Url
     *
     * @return mixed
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        return $baseUrl;
    }
    /**
     * Get Enable Push Notification
     *
     * @return mixed
     */
    public function getEnablePushNotification()
    {
        return $this->getConfigValue(self::XML_PATH_ENABLEPUSHNOTIFICATION);
    }
    /**
     * Get Apikey
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getConfigValue(self::XML_PATH_APIKEY);
    }
    /**
     * Get Authdomain
     *
     * @return mixed
     */
    public function getAuthdomain()
    {
        return $this->getConfigValue(self::XML_PATH_AUTHDOMAIN);
    }
    /**
     * Get Database Url
     *
     * @return mixed
     */
    public function getDatabaseUrl()
    {
        return $this->getConfigValue(self::XML_PATH_DATABASEURL);
    }
    /**
     * Get Project Id
     *
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->getConfigValue(self::XML_PATH_PROJECTID);
    }
    /**
     * Get Storage Bucket
     *
     * @return mixed
     */
    public function getStorageBucket()
    {
        return $this->getConfigValue(self::XML_PATH_STORAGEBUCKET);
    }
    /**
     * Get Message Sender Id
     *
     * @return mixed
     */
    public function getMessageSenderId()
    {
        return $this->getConfigValue(self::XML_PATH_MESSAGESENDERID);
    }
    /**
     * Get Enable Prompt
     *
     * @return mixed
     */
    public function getPromptEnable()
    {
        $this->cacheTypeList->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        $this->cacheTypeList->cleanType(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        return $this->getConfigValue(self::XML_PATH_PROMPT_ENABLE);
    }
    /**
     * Get Prompt Text
     *
     * @return mixed
     */
    public function getPromptText()
    {
        return $this->getConfigValue(self::XML_PATH_PROMPT_TEXT);
    }
    /**
     * Get Propmt Show Delay Seconds
     *
     * @return mixed
     */
    public function getShowDelaySeconds()
    {
        $ShowDelaySeconds=$this->getConfigValue(self::XML_PATH_PROMPT_SHOW_DELAY);
        if ($ShowDelaySeconds == '0') {
            $ShowDelaySeconds = 1;
        }
        return $ShowDelaySeconds;
    }
    /**
     * Get Prompt Frequency
     *
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->getConfigValue(self::XML_PATH_PROMPT_FREQUENCY);
    }
    /**
     * Get Prompt Pages
     *
     * @return mixed
     */
    public function getPages()
    {
        return $this->getConfigValue(self::XML_PATH_PROMPT_PAGES);
    }
    /**
     * Get Prompt Page Urls
     *
     * @return mixed
     */
    public function getPageUrl()
    {
        $pageUrl = $this->getConfigValue(self::XML_PATH_PROMPT_PAGE_URL);
        $pageUrl = explode(',', $pageUrl);
        return $pageUrl;
    }
    /**
     * Get Customer Id
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getId();
        } else {
            return "Guest";
        }
    }
    /**
     * Get Ip
     *
     * @return mixed
     */
    public function getIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }
}
