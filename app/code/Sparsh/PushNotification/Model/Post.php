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
namespace Sparsh\PushNotification\Model;

/**
 * @method Post setName($name)
 * @method Post setUrlKey($urlKey)
 * @method Post setPostContent($postContent)
 * @method Post setTags($tags)
 * @method Post setStatus($status)
 * @method Post setFeaturedImage($featuredImage)
 * @method Post setSampleCountrySelection($sampleCountrySelection)
 * @method Post setSampleUploadFile($sampleUploadFile)
 * @method Post setSampleMultiselect($sampleMultiselect)
 * @method mixed getName()
 * @method mixed getUrlKey()
 * @method mixed getPostContent()
 * @method mixed getTags()
 * @method mixed getStatus()
 * @method mixed getFeaturedImage()
 * @method mixed getSampleCountrySelection()
 * @method mixed getSampleUploadFile()
 * @method mixed getSampleMultiselect()
 * @method Post setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Post setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
/**
 * Class Post
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Post extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sparsh_pushnotification_post';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sparsh_pushnotification_post';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sparsh_pushnotification_post';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\PushNotification\Model\ResourceModel\Post::class
        );
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
