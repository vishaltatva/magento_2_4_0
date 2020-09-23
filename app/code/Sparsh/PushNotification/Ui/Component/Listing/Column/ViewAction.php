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
namespace Sparsh\PushNotification\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class ViewAction extends Column
{
    public $layout;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        array $components = [],
        array $data = []
    ) {
           $this->layout = $layout;
           parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['post_id'])) {
                    $item[$this->getData('name')] = $this->layout->createBlock(
                        \Magento\Backend\Block\Widget\Button::class,
                        '',
                        [
                            'data' => [
                                'label' => __('Send Test Notification'),
                                'type' => 'button',
                                'disabled' => false,
                                'class' => 'push_notification-grid-view',
                                'onclick' => 'push_notification.open(\''.$item['post_id'].'\')',
                            ]
                        ]
                    )->toHtml();
                }
            }
        }

        return $dataSource;
    }
}
