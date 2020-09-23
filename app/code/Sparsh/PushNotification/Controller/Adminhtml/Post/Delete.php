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
 * Class Delete
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Delete extends \Sparsh\PushNotification\Controller\Adminhtml\Post
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('post_id');
        if ($id) {
            try {
                $post = $this->_postFactory->create();
                $post->load($id);
                $name = $post->getName();
                $post->delete();
                $this->messageManager->addSuccess(__('The Template "'.$name.'" has been deleted.'));
                $resultRedirect->setPath('sparsh_push_notification/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('sparsh_push_notification/*/edit', ['post_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('Template to delete was not found.'));
        $resultRedirect->setPath('sparsh_push_notification/*/');
        return $resultRedirect;
    }
}
