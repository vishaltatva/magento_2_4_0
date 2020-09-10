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
            $name = "";
            try {
                $tokens = $this->_postFactory->create();
                $tokens->load($id);
                $name = $tokens->getName();
                $tokens->delete();
                $this->messageManager->addSuccess(__('The Template has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_sparsh_pushnotification_tokens_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('sparsh_pushnotification/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_sparsh_pushnotification_tokens_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('sparsh_pushnotification/*/edit', ['token_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addError(__('Template to delete was not found.'));
        $resultRedirect->setPath('sparsh_pushnotification/*/');
        return $resultRedirect;
    }
}
