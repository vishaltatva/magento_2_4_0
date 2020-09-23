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
  * Class RegisterDevice
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class RegisterDevice extends \Magento\Framework\App\Action\Action
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
     * @var \Sparsh\PushNotification\Model\TokensFactory
     */
    protected $tokensFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Sparsh\PushNotification\Model\TokensFactory $tokensFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Sparsh\PushNotification\Model\TokensFactory $tokensFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resource = $resource;
        $this->tokensFactory = $tokensFactory;
        $this->resultJsonFactory = $resultJsonFactory;
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

            $token = $post['token'];
            $device = $post['device'];
            $ip = $post['ip'];
            $customerId = $post['CustomerId'];

            $model = $this->tokensFactory->create();
            $connection = $this->_resource->getConnection();

            $select = $connection->select()
            ->from(
                ['o' => $this->_resource->getTableName('sparsh_push_notification_tokens')]
            )
                ->where('o.ip=?', $ip)
                ->where('o.device=?', $device);


            $result = count($connection->fetchAll($select));

            if ($result > 0) {
                $data = $connection->fetchAll($select);
                $tokenData = $data[0];
                if (($customerId == $tokenData['customer_id']) && ($token == $tokenData['token'])) {
                    $response = 'Device token is already added..!!';
                } else {
                    $model->load($tokenData['token_id']);
                    if (($customerId == $tokenData['customer_id'])) {
                        $model->setToken($token);
                    }
                    if (($customerId != $tokenData['customer_id'])) {
                           $model->setCustomerId($customerId);
                           $model->setToken($token);
                    }
                    $model->save();
                    $response = 'Device token is updated!!';
                }
                $success = false;
            } else {
                $model->setToken($token);
                $model->setDevice($device);
                $model->setIp($ip);
                $model->setCustomerId($customerId);
                $model->save();
                $response = 'Device token is successfully added!!';
                $success = true;
            }
             $resultJson = $this->resultJsonFactory->create();
             return $resultJson->setData([
                'result' => $response,
                'success' => $success
             ]);
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something went wrong. We can\'t register device token right now.')
            );
        }
    }
}
