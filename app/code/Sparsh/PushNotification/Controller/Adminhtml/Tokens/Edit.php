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
 * Class Edit
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Edit extends \Sparsh\PushNotification\Controller\Adminhtml\Tokens
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Sparsh\PushNotification\Model\TokensFactory $tokensFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Sparsh\PushNotification\Model\TokensFactory $tokensFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_backendSession    = $backendSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($tokensFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sparsh_PushNotification::tokens');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('token_id');
        $tokens = $this->_initTokens();
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Sparsh_PushNotification::tokens');
        $resultPage->getConfig()->getTitle()->set(__('Templates'));
        if ($id) {
            $tokens->load($id);
            if (!$tokens->getTokenId()) {
                $this->messageManager->addError(__('This Tokens no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'sparsh_pushnotification/*/edit',
                    [
                        'token_id' => $tokens->getTokenId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $tokens->getTokenId() ? $tokens->getName() : __('New Template');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->_backendSession->getData('sparsh_pushnotification_tokens_data', true);
        if (!empty($data)) {
            $tokens->setData($data);
        }
        return $resultPage;
    }
}
