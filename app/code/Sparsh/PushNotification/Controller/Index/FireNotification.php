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
  * Class FireNotification
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class FireNotification extends \Magento\Framework\App\Action\Action
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
     *  FormFactory
     *
     * @var \Sparsh\PushNotification\Model\PostFactory
     */
    protected $postFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Sparsh\PushNotification\Model\PostFactory $postFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Request\Http $request,
        \Sparsh\PushNotification\Model\PostFactory $postFactory
    ) {
        $this->_resource = $resource;
        $this->postFactory = $postFactory;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $id=$this->request->getParam('id');
        if (!$id) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $model = $this->postFactory->create();
            if (isset($id)) {
                $model->load($id);
            }
            $templateClick = $model->getTemplateClick();
            ++ $templateClick;
            $model->setTemplateClick($templateClick);
            $model->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something went wrong. We can\'t update count right now.')
            );
        }
    }
}
