<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\ScoreRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Score
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
     * @MongoDB\Field(type="int")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"List"})
     */
    protected $score;

    /**
     * @MongoDB\Field(type="date")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"List"})
     */
    protected $finishedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument=User::class)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"List"})
     */
    protected $user;

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setFinishedAt(\DateTime $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
