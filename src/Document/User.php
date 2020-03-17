<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;

/**
 * @MongoDB\Document
 * @Serializer\ExclusionPolicy("all")
 */
class User
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\UniqueIndex
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"List"})
     * @Serializer\SerializedName("id")
     */
    protected $uuid;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\UniqueIndex
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"List"})
     */
    protected $name;

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
