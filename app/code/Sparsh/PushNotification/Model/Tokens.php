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
 * @method Tokens setName($name)
 * @method Tokens setUrlKey($urlKey)
 * @method Tokens setTokensContent($tokensContent)
 * @method Tokens setTags($tags)
 * @method Tokens setStatus($status)
 * @method Tokens setFeaturedImage($featuredImage)
 * @method Tokens setSampleCountrySelection($sampleCountrySelection)
 * @method Tokens setSampleUploadFile($sampleUploadFile)
 * @method mixed getName()
 * @method mixed getUrlKey()
 * @method mixed getTokensContent()
 * @method mixed getTags()
 * @method mixed getStatus()
 * @method mixed getFeaturedImage()
 * @method mixed getSampleCountrySelection()
 * @method mixed getSampleUploadFile()
 * @method Tokens setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Tokens setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
/**
 * Class Tokens
 *
 * @category Sparsh
 * @package  Sparsh_PushNotification
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Tokens extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sparsh_push_notification_tokens';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sparsh_push_notification_tokens';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sparsh_push_notification_tokens';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Sparsh\PushNotification\Model\ResourceModel\Tokens::class
        );
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getTokenId()];
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
