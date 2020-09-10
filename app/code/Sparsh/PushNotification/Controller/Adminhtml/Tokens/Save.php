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
 * Class Save
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Save extends \Sparsh\PushNotification\Controller\Adminhtml\Tokens
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
     * @var \Sparsh\PushNotification\Model\Tokens\File
     */
    protected $_fileModel;

    /**
     * Image model
     *
     * @var \Sparsh\PushNotification\Model\Tokens\Image
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
     * @param \Sparsh\PushNotification\Model\Tokens\File $fileModel
     * @param \Sparsh\PushNotification\Model\Tokens\Image $imageModel
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Sparsh\PushNotification\Model\TokensFactory $tokensFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Sparsh\PushNotification\Model\Upload $uploadModel,
        \Sparsh\PushNotification\Model\Tokens\File $fileModel,
        \Sparsh\PushNotification\Model\Tokens\Image $imageModel,
        \Magento\Backend\Model\Session $backendSession,
        \Sparsh\PushNotification\Model\TokensFactory $tokensFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_uploadModel    = $uploadModel;
        $this->_fileModel      = $fileModel;
        $this->_imageModel     = $imageModel;
        $this->_backendSession = $backendSession;
        parent::__construct($tokensFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams('token');
        $data = $params['tokens'];

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $tokens = $this->_initTokens();
            $tokens->setData($data);
            $this->_eventManager->dispatch(
                'sparsh_pushnotification_tokens_prepare_save',
                [
                    'tokens' => $tokens,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $tokens->save();
                $this->messageManager->addSuccess(__('The Template has been saved.'));
                $this->_backendSession->setSparshPushNotificationTokensData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'sparsh_pushnotification/*/edit',
                        [
                            'token_id' => $tokens->getTokenId(),
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
            $this->_getSession()->setSparshPushNotificationTokensData($data);
            $resultRedirect->setPath(
                'sparsh_pushnotification/*/edit',
                [
                    'token_id' => $tokens->getTokenId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('sparsh_pushnotification/*/');
        return $resultRedirect;
    }
}
