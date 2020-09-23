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
namespace Sparsh\PushNotification\Block\Adminhtml\Post\Edit\Tab;

 /**
* Class Post
*
* @category Sparsh
* @package  Sparsh_PushNotification
* @author   Sparsh <magento@sparsh-technologies.com>
* @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
* @link     https://www.sparsh-technologies.com
*/
class Post extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_countryOptions;

    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_booleanOptions;

    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroup;

    /**
     * SystemStore
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * constructor
     *
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Locale\Country $countryOptions
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Locale\Country $countryOptions,
        \Sparsh\PushNotification\Model\Config\Source\ActiveInactive $booleanOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_wysiwygConfig            = $wysiwygConfig;
        $this->_countryOptions           = $countryOptions;
        $this->_booleanOptions           = $booleanOptions;
        $this->customerGroup             = $customerGroup;
        $this->systemStore               = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $post = $this->_coreRegistry->registry('sparsh_push_notification_post');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addType(
            'image',
            \Sparsh\PushNotification\Block\Adminhtml\Post\Helper\Image::class
        );

        if ($post->getId()) {
            $fieldset->addField(
                'post_id',
                'hidden',
                ['name' => 'post_id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'note' => 'Enter name of the template for internal reference.',
            ]
        );
        $fieldset->addField(
            'schedule',
            'date',
            [
                'name' => 'schedule',
                'label' => __('Schedule Datetime'),
                'id' => 'schedule',
                'title' => __('Schedule Datetime'),
                'date_format' => 'yyyy-MM-dd',
                'class' => 'required-entry validate-date validate-date-range date-range-attribute-from',
                'time_format' => 'HH:mm:ss',
                'required' => true,
            ]
        );

        $customerGroups = $this->customerGroup->toOptionArray();
        $fieldset->addField(
            'customer_groups',
            'multiselect',
            [
                'name' => 'customer_groups[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' => $customerGroups
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_view',
                'multiselect',
                [
                    'name' => 'store_view[]',
                    'label' => __('Store Views'),
                    'title' => __('Store Views'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_view',
                'hidden',
                ['name' => 'store_view[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $post->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'status',
            'select',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_booleanOptions->toOptionArray(),
            ]
        );

        $postData = $this->_session->getData('sparsh_push_notification_post_data', true);
        if ($postData) {
            $post->addData($postData);
        } else {
            if (!$post->getId()) {
                $post->addData($post->getDefaultValues());
            }
        }
        $form->addValues($post->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
