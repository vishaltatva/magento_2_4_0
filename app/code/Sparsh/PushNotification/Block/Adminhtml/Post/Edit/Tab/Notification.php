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
  * Class Notification
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class Notification extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
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
        $notificationFieldset = $form->addFieldset(
            'notification_fieldset',
            [
              'class' => 'fieldset-wide'
            ]
        );

        $notificationFieldset->addType(
            'image',
            \Sparsh\PushNotification\Block\Adminhtml\Post\Helper\Image::class
        );

        if ($post->getId()) {
            $notificationFieldset->addField(
                'post_id',
                'hidden',
                ['name' => 'post_id']
            );
        }
        $notificationFieldset->addField(
            'template_title',
            'text',
            [
                'name'  => 'template_title',
                'label' => __('Notification Title'),
                'title' => __('Notification Title'),
                'required' => true,
            ]
        );
        $notificationFieldset->addField(
            'template_message',
            'editor',
            [
                'name'  => 'template_message',
                'label' => __('Notification Message'),
                'title' => __('Notification Message'),
                'config'    => 0,
                'required' => true,
            ]
        );
        $notificationFieldset->addField(
            'template_tags',
            'text',
            [
                'name'  => 'template_tags',
                'label' => __('Tags'),
                'title' => __('Tags'),
            ]
        );
        $notificationFieldset->addField(
            'template_logo',
            'image',
            [
                'name'  => 'template_logo',
                'label' => __('Notification Image'),
                'title' => __('Notification Image'),
                'note' => 'Allowed file types: jpg, jpeg, gif, png. Recommended width to height ratio is 1:1 (e.g. 360*360px, 720*720px).',
            ]
        );
        $notificationFieldset->addField(
            'redirect_url',
            'text',
            [
                'name'  => 'redirect_url',
                'label' => __('Notification Click Url'),
                'title' => __('Notification Click Url'),
                'note' => 'Enter a link you want user to redirect on clicking of the notification pop-up.',
            ]
        );

        $notificationFieldset->addField(
            'utm_parameters',
            'text',
            [
                'name'  => 'utm_parameters',
                'label' => __('UTM Parameters For Tracking'),
                'title' => __('UTM Parameters For Tracking'),
                'placeholder' => __('utm_source=google&utm_medium=cpc'),
                'note' => 'Use UTM parameters as an advanced GA tracking option to track visitors interaction activities.',
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
        return __('Notification Settings');
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
