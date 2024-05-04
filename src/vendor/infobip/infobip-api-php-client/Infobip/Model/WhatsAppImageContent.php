<?php
/**
 * WhatsAppImageContent
 *
 * PHP version 7.2
 *
 * @category Class
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 */

/**
 * Infobip Client API Libraries OpenAPI Specification
 *
 * OpenAPI specification containing public endpoints supported in client API libraries.
 *
 * Contact: support@infobip.com
 *
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * Do not edit the class manually.
 */

namespace Infobip\Model;

use ArrayAccess;
use Infobip\ObjectSerializer;

/**
 * WhatsAppImageContent Class Doc Comment
 *
 * @category Class
 * @description The content object to build a message that will be sent.
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null
 */
class WhatsAppImageContent implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'WhatsAppImageContent';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'mediaUrl' => 'string',
        'caption' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'mediaUrl' => null,
        'caption' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'mediaUrl' => 'mediaUrl',
        'caption' => 'caption'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'mediaUrl' => 'setMediaUrl',
        'caption' => 'setCaption'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'mediaUrl' => 'getMediaUrl',
        'caption' => 'getCaption'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }





    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['mediaUrl'] = $data['mediaUrl'] ?? null;
        $this->container['caption'] = $data['caption'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['mediaUrl'] === null) {
            $invalidProperties[] = "'mediaUrl' can't be null";
        }
        if ((mb_strlen($this->container['mediaUrl']) > 2048)) {
            $invalidProperties[] = "invalid value for 'mediaUrl', the character length must be smaller than or equal to 2048.";
        }

        if ((mb_strlen($this->container['mediaUrl']) < 1)) {
            $invalidProperties[] = "invalid value for 'mediaUrl', the character length must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['caption']) && (mb_strlen($this->container['caption']) > 3000)) {
            $invalidProperties[] = "invalid value for 'caption', the character length must be smaller than or equal to 3000.";
        }

        if (!is_null($this->container['caption']) && (mb_strlen($this->container['caption']) < 0)) {
            $invalidProperties[] = "invalid value for 'caption', the character length must be bigger than or equal to 0.";
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets mediaUrl
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->container['mediaUrl'];
    }

    /**
     * Sets mediaUrl
     *
     * @param string $mediaUrl URL of an image sent in a WhatsApp message. Must be a valid URL starting with `https://` or `http://`. Supported image types are `JPG`, `JPEG`, `PNG`. Maximum image size is 5MB.
     *
     * @return self
     */
    public function setMediaUrl($mediaUrl)
    {
        if ((mb_strlen($mediaUrl) > 2048)) {
            throw new \InvalidArgumentException('invalid length for $mediaUrl when calling WhatsAppImageContent., must be smaller than or equal to 2048.');
        }
        if ((mb_strlen($mediaUrl) < 1)) {
            throw new \InvalidArgumentException('invalid length for $mediaUrl when calling WhatsAppImageContent., must be bigger than or equal to 1.');
        }

        $this->container['mediaUrl'] = $mediaUrl;

        return $this;
    }

    /**
     * Gets caption
     *
     * @return string|null
     */
    public function getCaption()
    {
        return $this->container['caption'];
    }

    /**
     * Sets caption
     *
     * @param string|null $caption Caption of the image.
     *
     * @return self
     */
    public function setCaption($caption)
    {
        if (!is_null($caption) && (mb_strlen($caption) > 3000)) {
            throw new \InvalidArgumentException('invalid length for $caption when calling WhatsAppImageContent., must be smaller than or equal to 3000.');
        }
        if (!is_null($caption) && (mb_strlen($caption) < 0)) {
            throw new \InvalidArgumentException('invalid length for $caption when calling WhatsAppImageContent., must be bigger than or equal to 0.');
        }

        $this->container['caption'] = $caption;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
