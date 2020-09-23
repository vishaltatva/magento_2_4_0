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
namespace Sparsh\PushNotification\Controller\Adminhtml\Tokens;

/**
 * Class Delete
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Delete extends \Sparsh\PushNotification\Controller\Adminhtml\Tokens
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('token_id');
        if ($id) {
            try {
                $tokens = $this->_postFactory->create();
                $tokens->load($id);
                $tokens->delete();
                $this->messageManager->addSuccess(__('The device token has been deleted.'));
                $resultRedirect->setPath('sparsh_push_notification/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('sparsh_push_notification/*/', ['token_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('Device token to delete was not found.'));
        $resultRedirect->setPath('sparsh_push_notification/*/');
        return $resultRedirect;
    }
}
