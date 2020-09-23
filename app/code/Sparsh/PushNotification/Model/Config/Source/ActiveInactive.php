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
namespace Sparsh\PushNotification\Model\Config\Source;

/**
  * Class ActiveInactive
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class ActiveInactive implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Active')], ['value' => 0, 'label' => __('In Active')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
