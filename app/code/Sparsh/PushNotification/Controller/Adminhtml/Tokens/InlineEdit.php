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
 * Class InlineEdit
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonFactory;

    /**
     * Tokens Factory
     *
     * @var \Sparsh\PushNotification\Model\TokensFactory
     */
    protected $_postFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Sparsh\PushNotification\Model\TokensFactory $tokensFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Sparsh\PushNotification\Model\TokensFactory $tokensFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_postFactory = $tokensFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $tokensItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($tokensItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($tokensItems) as $tokensId) {
            $tokens = $this->_postFactory->create()->load($tokensId);
            try {
                $tokensData = $tokensItems[$tokensId];
                $tokens->addData($tokensData);
                $tokens->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithTokensId($tokens, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithTokensId($tokens, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithTokensId(
                    $tokens,
                    __('Something went wrong while saving the Tokens.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Tokens id to error message
     *
     * @param \Sparsh\PushNotification\Model\Tokens $tokens
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTokensId(\Sparsh\PushNotification\Model\Tokens $tokens, $errorText)
    {
        return '[Tokens ID: ' . $tokens->getTokenId() . '] ' . $errorText;
    }
}
