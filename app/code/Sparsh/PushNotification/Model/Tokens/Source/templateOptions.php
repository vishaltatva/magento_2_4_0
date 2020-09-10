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
namespace Sparsh\PushNotification\Model\Tokens\Source;

 /**
  * Class templateOptions
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class templateOptions implements \Magento\Framework\Option\ArrayInterface
{
    
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }
    
    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => __('Select Template'), 'value' => 'post_id'];
        foreach ($this->getTemplatefield() as $field) {
            $options[] = ['label' => $field['template_title'], 'value' => $field['post_id']];
        }
        return $options;
    }
    
    public function getTemplatefield()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $customTable = $connection->getTableName('sparsh_pushnotification_post');
        $allField = $connection->fetchAll("SELECT * FROM `".$customTable."`");
        return $allField;
    }
}
