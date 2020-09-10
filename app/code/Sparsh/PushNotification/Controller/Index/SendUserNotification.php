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
namespace Sparsh\PushNotification\Controller\Index;

 /**
  * Class SendUserNotification
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class SendUserNotification extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * HelperData
     *
     * @var \Sparsh\PushNotification\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     *  FormFactory
     *
     * @var \Sparsh\PushNotification\Model\TokensFactory
     */
    protected $tokensFactory;
    /**
     *  FormFactory
     *
     * @var \Sparsh\PushNotification\Model\PostFactory
     */
    protected $postFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Sparsh\PushNotification\Helper\Data $helperData
     * @param \Sparsh\PushNotification\Model\TokensFactory $tokensFactory
     * @param \Sparsh\PushNotification\Model\PostFactory $postFactory
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Sparsh\PushNotification\Helper\Data $helperData,
        \Sparsh\PushNotification\Model\TokensFactory $tokensFactory,
        \Sparsh\PushNotification\Model\PostFactory $postFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_resource = $resource;
        $this->helperData = $helperData;
        $this->tokensFactory = $tokensFactory;
        $this->postFactory = $postFactory;
        $this->curl = $curl;
        $this->date =  $date;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        try {
            $date = $this->date->date()->format('Y-m-d H:i:s');
            $postCollections = $this->postFactory->create()->getCollection()
                                                           ->addFieldToFilter('status', 1)
                                                           ->addFieldToFilter('schedule_status', 0)
                                                           ->addFieldToFilter('schedule', ['lteq' => $date]);
            $templateData = [];
            $i=0;
            foreach ($postCollections as $value) {
                    $templateData[$i]['post_id']= $value->getPostId();
                    $templateData[$i]['template_title'] = $value->getTemplateTitle();
                    $templateData[$i]['template_messege'] =$value->getTemplateMessege();
                    $templateData[$i]['redirect_url'] = $value->getRedirectUrl();
                    $templateData[$i]['utm_parameters'] = $value->getUtmParameters();
                    $templateData[$i]['template_logo'] = $value->getTemplateLogo();
                    $templateData[$i]['customer_groups'] = $value->getCustomerGroups();
                    $templateData[$i]['store_view'] = $value->getStoreView();
                    ++$i;
            }
            if (!empty($templateData)) {
                $this->sendToCustomer($templateData);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process send Notification request right now. Sorry, that\'s all we know.')
            );
        }
    }
    /**
     * Send the notification base on customer
     *
     * @param array $templateData
     * @return void
     */
    public function sendToCustomer($templateData)
    {
        $j=0;
        $customers=[];
        $customerTemplateData=[];
        foreach ($templateData as $value) {
            $customerlist = $this->customerFactory->create()->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('store_id', explode(',', $value['store_view']))
            ->addFieldToFilter('group_id', explode(',', $value['customer_groups']));
            if (!empty($customerlist->getData())) {
                $customers[$j]=$customerlist->getData();
                $customerTemplateData[$j]=$value;
            } else {
                $customers[$j]=[];
            }
            ++$j;
        }
        $tokenList = [];
        for ($k=0; $k <count($customers); $k++) {
            if (empty($customers[$k])) {
                $tokenList[$k]=[];
            } else {
                $tokenCollections = $this->tokensFactory->create()->getCollection()
                ->addFieldToSelect('token')
                ->addFieldToFilter('customer_id', ['neq' =>'Guest'])
                ->addFieldToFilter('customer_id', array_column($customers[$k], 'entity_id'));
                if (!empty($tokenCollections->getData())) {
                    $tokenList[$k]=array_column($tokenCollections->getData(), 'token');
                }
            }
        }

            $guestTokenList = $this->sendToGuestCustomer($templateData);
        if (!empty($guestTokenList)) {
            $this->sendNotification($templateData, $tokenList, $guestTokenList);
        } else {
            if (!empty($tokenList)) {
                $this->sendCustomerNotification($templateData, $tokenList);
            }
                                        
        }
    }
    /**
     * Retrieve guest user list
     *
     * @param array $templateData
     * @return array
     */
    public function sendToGuestCustomer($templateData)
    {
        $guestTokenCollections = $this->tokensFactory->create()
                    ->getCollection()
                    ->addFieldToSelect('token')
                    ->addFieldToFilter('customer_id', ['eq' =>'Guest']);
        $guestTokenList = [];
        foreach ($guestTokenCollections as $value) {
                $guestTokenList[] = $value->getToken();
        }
        if (!empty($guestTokenList)) {
            return $guestTokenList;
        } else {
            return [];
        }
    }
    /**
     * Send Notification for customer only
     *
     * @param array $templateData
     * @param array $tokenList
     * @return void
     */
    public function sendCustomerNotification($templateData, $tokenList)
    {
        $mediaUrl=$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        define('API_ACCESS_KEY', $this->helperData->getServerKey());
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $i=0;
        foreach ($templateData as $value) {
            if (empty($tokenList[$i])) {
                ++$i;
                continue;
            }
                $imageUrl = $mediaUrl.'sparsh/pushnotification/post/image'.$value['template_logo'];
                $notification = [
                    'title' =>$value['template_title'],
                    'body' => $value['template_messege'],
                    'icon' => $imageUrl,
                    'click_action' => $value['redirect_url'],
                    'sound' => 'mySound',
                    "templateId" =>$value['post_id'],
                    "utm_parameters" =>$value['utm_parameters']
                ];
                $fcmNotification = [
                    'registration_ids' => $tokenList[$i],
                    'data' => $notification
                ];
                $fcmNotification = json_encode($fcmNotification);
                $headers = ["Authorization" => "key=".API_ACCESS_KEY, "Content-Type" => "application/json"];
                $this->curl->setHeaders($headers);
                $this->curl->post($fcmUrl, $fcmNotification);
                $response = $this->curl->getBody();
                $this->setSchedule($value['post_id']);
                ++$i;
        }
    }
    /**
     * Send Notification for both guest and customer
     *
     * @param array $templateData
     * @param array $tokenList
     * @param array $guestTokenList
     * @return void
     */
    public function sendNotification($templateData, $tokenList, $guestTokenList)
    {
        $mediaUrl=$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        define('API_ACCESS_KEY', $this->helperData->getServerKey());
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $i=0;
        foreach ($templateData as $value) {
            if (!empty($tokenList[$i])) {
                $tokens =array_merge($guestTokenList, $tokenList[$i]);
            } else {
                $tokens =$guestTokenList;
            }
                $imageUrl = $mediaUrl.'sparsh/pushnotification/post/image'.$value['template_logo'];
                $notification = [
                    'title' =>$value['template_title'],
                    'body' => $value['template_messege'],
                    'icon' => $imageUrl,
                    'click_action' => $value['redirect_url'],
                    'sound' => 'mySound',
                    "templateId" =>$value['post_id'],
                    "utm_parameters" =>$value['utm_parameters']
                ];
                $fcmNotification = [
                    'registration_ids' => $tokens,
                    'data' => $notification
                ];
                $fcmNotification = json_encode($fcmNotification);
                $headers = ["Authorization" => "key=".API_ACCESS_KEY, "Content-Type" => "application/json"];
                $this->curl->setHeaders($headers);
                $this->curl->post($fcmUrl, $fcmNotification);
                $response = $this->curl->getBody();
                $this->setSchedule($value['post_id']);
                ++$i;
        }
    }
    /**
     * Change schedule status
     *
     * @param Int $id
     * @return void
     */
    public function setSchedule($id)
    {
        $model = $this->postFactory->create();
        $model->load($id);
        $model->setScheduleStatus(1);
        $model->setStatus(0);
        $model->save();
    }
}
