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
namespace Sparsh\PushNotification\Model\Post;

 /**
  * Class Image
  *
  * @category Sparsh
  * @package  Sparsh_PushNotification
  * @author   Sparsh <magento@sparsh-technologies.com>
  * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
  * @link     https://www.sparsh-technologies.com
  */
class Image
{
    /**
     * Media sub folder
     *
     * @var string
     */
    protected $_subDir = 'sparsh/push_notification';

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * File system model
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Filesystem $fileSystem
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl(
            ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
        ).$this->_subDir.'/image';
    }
    /**
     * get base image dir
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_fileSystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        )->getAbsolutePath($this->_subDir.'/image');
    }
}
