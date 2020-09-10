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
  * Class SendTestNotification
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class SendTestNotification extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     *  FormFactory
     *
     * @var \Sparsh\PushNotification\Model\PostFactory
     */
    protected $postFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Sparsh\PushNotification\Model\PostFactory $postFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Sparsh\PushNotification\Model\PostFactory $postFactory
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->postFactory = $postFactory;
        return parent::__construct($context);
    }

    public function execute()
    {

        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }

        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);
            $mediaUrl=$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $templateId = $post['templateId'];
            $postCollections = $this->postFactory->create()->getCollection()
                                      ->addFieldToFilter('post_id', $templateId);
            $templateData = [];
            foreach ($postCollections as $value) {
                $templateData['template_title'] = $value->getTemplateTitle();
                $templateData['template_messege'] =$value->getTemplateMessege();
                $templateData['redirect_url'] = $value->getRedirectUrl();
                $templateData['template_logo'] = $value->getTemplateLogo();
            }
            if (isset($templateData['template_logo'])) {
                $imageUrl = $mediaUrl.'sparsh/pushnotification/post/image'.$templateData['template_logo'];
            } else {
                $imageUrl = '';
            }
            $notification = [
                'title' =>$templateData['template_title'],
                'body' => $templateData['template_messege'],
                'icon' => $imageUrl,
                'click_action' => $templateData['redirect_url'],
            ];
    
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData([
                'template' => $notification
            ]);
            
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process send Test Notification right now.')
            );
        }
    }
}
