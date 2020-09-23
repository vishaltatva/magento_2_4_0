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
namespace Sparsh\PushNotification\Setup;

 /**
  * Class InstallSchema
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('sparsh_push_notification_post')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('sparsh_push_notification_post')
            )
            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Template ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Template Name'
            )
            ->addColumn(
                'template_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Template Title'
            )
            ->addColumn(
                'template_message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Template Message'
            )
            ->addColumn(
                'customer_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable => false'],
                'Customer Groups'
            )
            ->addColumn(
                'store_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable => false'],
                'Store View'
            )
            ->addColumn(
                'redirect_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Redirect Url'
            )
            ->addColumn(
                'utm_parameters',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'UTM Parameters'
            )
            ->addColumn(
                'template_tags',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Template Tags'
            )
            ->addColumn(
                'template_click',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                100,
                ['default' => 0,'nullable' => false],
                'Template Click Count'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                1,
                [],
                'Template Status'
            )
            ->addColumn(
                'schedule_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                1,
                ['default' => 0,'nullable' => false],
                'Template schedule status'
            )
            ->addColumn(
                'template_logo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Template Logo'
            )
            ->addColumn(
                'schedule',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Schedule Date Time'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Template Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Template Updated At'
            )
            ->setComment('Sparsh Push Notification Template Table');
            $installer->getConnection()->createTable($table);
            $installer->getConnection()->addIndex(
                $installer->getTable('sparsh_push_notification_post'),
                $setup->getIdxName(
                    $installer->getTable('sparsh_push_notification_post'),
                    ['name','template_title','template_message','redirect_url','template_tags','template_logo'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name','template_title','template_message','redirect_url','template_tags','template_logo'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }

        if (!$installer->tableExists('sparsh_push_notification_tokens')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('sparsh_push_notification_tokens')
                )
                 ->addColumn(
                     'token_id',
                     \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                     null,
                     [
                         'identity' => true,
                         'nullable' => false,
                         'primary'  => true,
                         'unsigned' => true,
                     ],
                     'Token ID'
                 )
                 ->addColumn(
                     'ip',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                     255,
                     ['nullable => false'],
                     'Ip Address'
                 )
                 ->addColumn(
                     'customer_id',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                     255,
                     ['nullable => false'],
                     'Customer Id'
                 )
                 ->addColumn(
                     'device',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                     255,
                     ['nullable => false'],
                     'Device'
                 )
                 ->addColumn(
                     'token',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                     255,
                     ['nullable => false'],
                     'Token'
                 )
                 ->addColumn(
                     'created_at',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                     null,
                     [],
                     'Token Created At'
                 )
                 ->addColumn(
                     'updated_at',
                     \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                     null,
                     [],
                     'Token Updated At'
                 )
                 ->setComment('Sparsh Push Notification Token Table');
                 $installer->getConnection()->createTable($table);
                 $installer->getConnection()->addIndex(
                     $installer->getTable('sparsh_push_notification_tokens'),
                     $setup->getIdxName(
                         $installer->getTable('sparsh_push_notification_tokens'),
                         ['ip','device','token'],
                         \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                     ),
                     ['ip','device','token'],
                     \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                 );
        }
        $installer->endSetup();
    }
}
