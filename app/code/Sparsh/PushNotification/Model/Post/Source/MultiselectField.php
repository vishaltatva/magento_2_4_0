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
namespace Sparsh\PushNotification\Model\Post\Source;

/**
 * Class MultiselectField
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class MultiselectField implements \Magento\Framework\Option\ArrayInterface
{
    const OPTION_1 = 1;
    const OPTION_2 = 2;

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::OPTION_1,
                'label' => __('Option 1')
            ],
            [
                'value' => self::OPTION_2,
                'label' => __('Option 2')
            ],
        ];
        return $options;
    }
}
