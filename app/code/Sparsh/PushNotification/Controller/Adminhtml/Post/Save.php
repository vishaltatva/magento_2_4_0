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
namespace Sparsh\PushNotification\Controller\Adminhtml\Post;

/**
 * Class Save
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Save extends \Sparsh\PushNotification\Controller\Adminhtml\Post
{
    /**
     * Upload model
     *
     * @var \Sparsh\PushNotification\Model\Upload
     */
    protected $_uploadModel;

    /**
     * File model
     *
     * @var \Sparsh\PushNotification\Model\Post\File
     */
    protected $_fileModel;

    /**
     * Image model
     *
     * @var \Sparsh\PushNotification\Model\Post\Image
     */
    protected $_imageModel;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * constructor
     *
     * @param \Sparsh\PushNotification\Model\Upload $uploadModel
     * @param \Sparsh\PushNotification\Model\Post\File $fileModel
     * @param \Sparsh\PushNotification\Model\Post\Image $imageModel
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Sparsh\PushNotification\Model\PostFactory $postFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Sparsh\PushNotification\Model\Upload $uploadModel,
        \Sparsh\PushNotification\Model\Post\File $fileModel,
        \Sparsh\PushNotification\Model\Post\Image $imageModel,
        \Magento\Backend\Model\Session $backendSession,
        \Sparsh\PushNotification\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_uploadModel    = $uploadModel;
        $this->_fileModel      = $fileModel;
        $this->_imageModel     = $imageModel;
        $this->_backendSession = $backendSession;
        parent::__construct($postFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('post');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->_filterData($data);
            $post = $this->_initPost();
            $data['store_view'] = implode(",", $data['store_view']);
            $data['customer_groups'] = implode(",", $data['customer_groups']);
            if ($data['status']) {
                $data['schedule_status'] = 0;
            }
            if (!$data['status']) {
                $data['schedule_status'] = 2;
            }
            if (!isset($data['post_id']) && !$data['status']) {
                $data['schedule_status'] = 0;
            }
            $post->setData($data);
            $featuredImage = $this->_uploadModel->uploadFileAndGetName(
                'template_logo',
                $this->_imageModel->getBaseDir(),
                $data
            );
            $post->setTemplateLogo($featuredImage);
            $this->_eventManager->dispatch(
                'sparsh_pushnotification_post_prepare_save',
                [
                    'post' => $post,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $post->save();
                $this->messageManager->addSuccess(__('The Template has been saved.'));
                $this->_backendSession->setSparshPushNotificationPostData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'sparsh_pushnotification/*/edit',
                        [
                            'post_id' => $post->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('sparsh_pushnotification/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Template.'));
            }
            $this->_getSession()->setSparshPushNotificationPostData($data);
            $resultRedirect->setPath(
                'sparsh_pushnotification/*/edit',
                [
                    'post_id' => $post->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('sparsh_pushnotification/*/');
        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     * @return array
     */
    protected function _filterData($data)
    {
        if (isset($data['sample_multiselect'])) {
            if (is_array($data['sample_multiselect'])) {
                $data['sample_multiselect'] = implode(',', $data['sample_multiselect']);
            }
        }
        return $data;
    }
}
