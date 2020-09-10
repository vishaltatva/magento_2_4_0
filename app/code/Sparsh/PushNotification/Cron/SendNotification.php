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
namespace Sparsh\PushNotification\Cron;

/**
 * Class SendNotification
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class SendNotification
{
    /**
     * @var \Sparsh\PushNotification\Controller\Index\SendUserNotification sendUserNotification
     */
    private $sendUserNotification;

    public function __construct(
        \Sparsh\PushNotification\Controller\Index\SendUserNotification $sendUserNotification
    ) {
        $this->sendUserNotification = $sendUserNotification;
    }
    public function execute()
    {
        $this->sendUserNotification->execute();
        return $this;
    }
}
