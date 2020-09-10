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
namespace Sparsh\PushNotification\Block\Adminhtml\Tokens\Edit\Tab;

/**
 * Class Tokens
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Tokens extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
    protected $_templateOptions;

    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_booleanOptions;

    /**
     * Sample Multiselect options
     *
     * @var \Sparsh\PushNotification\Model\Tokens\Source\SampleMultiselect
     */
    protected $_sampleMultiselectOptions;

    /**
     * constructor
     *
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Locale\Country $countryOptions
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Sparsh\PushNotification\Model\Tokens\Source\SampleMultiselect $sampleMultiselectOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Sparsh\PushNotification\Model\Tokens\Source\templateOptions $templateOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_wysiwygConfig            = $wysiwygConfig;
        $this->_templateOptions           = $templateOptions;
        $this->_booleanOptions           = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $tokens = $this->_coreRegistry->registry('sparsh_pushnotification_tokens');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tokens_');
        $form->setFieldNameSuffix('tokens');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'class'  => 'fieldset-wide'
            ]
        );
        
        $fieldset->addType(
            'image',
            \Sparsh\PushNotification\Block\Adminhtml\Tokens\Helper\Image::class
        );
        $fieldset->addType(
            'file',
            \Sparsh\PushNotification\Block\Adminhtml\Tokens\Helper\File::class
        );

        if ($tokens->getId()) {
            $fieldset->addField(
                'token_id',
                'hidden',
                ['name' => 'token_id']
            );
        }
        $fieldset->addField(
            'token',
            'text',
            [
                'name'  => 'token',
                'label' => __('Token'),
                'title' => __('Token'),
            ]
        );
        $fieldset->addField(
            'template_id',
            'select',
            [
                'name'  => 'template_id',
                'label' => __('Select Template'),
                'title' => __('Select Template'),
                'values' => $this->_templateOptions->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'template_click',
            'text',
            [
                'name'  => 'template_click',
                'label' => __('Template Click'),
                'title' => __('Template Click')
            ]
        );

        $tokensData = $this->_session->getData('sparsh_pushnotification_tokens_data', true);
        if ($tokensData) {
            $tokens->addData($tokensData);
        } else {
            if (!$tokens->getId()) {
                $tokens->addData($tokens->getDefaultValues());
            }
        }
        $form->addValues($tokens->getData());
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
        return __('Tokens');
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
