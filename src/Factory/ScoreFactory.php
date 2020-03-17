<?php

namespace App\Factory;

use App\Document\Score;
use App\Document\User;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\ODM\MongoDB\DocumentManager;

class ScoreFactory implements FactoryInterface
{
    private const VALID_SCORE_FIELDS = [
        'id'          => '/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/',
        'score'       => '/^\d+$/',
        'finished_at' => 'isDate()',
        'user'        => null,
    ];

    private const VALID_USER_FIELDS = [
        'id'          => '/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/',
        'name'        => '/^[a-zA-Z0-9\s-]+/',
    ];

    /**
     * @var DocumentManager
     */
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function build(array $data): void
    {
        $this->validateDatSource($data);

        $purger = new MongoDBPurger($this->documentManager);
        $purger->purge();

        foreach ($data as $element) {
            $user = new User();
            $user->setUuid($element['user']['id']);
            $user->setName($element['user']['name']);

            $this->documentManager->persist($user);

            $score = new Score();
            $score->setScore($element['score']);
            $score->setUuid($element['id']);
            $score->setFinishedAt(new \DateTime($element['finished_at']));
            $score->setUser($user);

            $this->documentManager->persist($score);
            $this->documentManager->flush();
        }
    }

    private function validateDatSource(array $data)
    {
        foreach ($data as $element) {
            $this->validateElement($element);
        }
    }

    private function validateElement(array $element)
    {
        $checkedScoreFields = [];
        $checkedUserFields = [];

        foreach ($element as $key => $value) {
            if (!array_key_exists($key, self::VALID_SCORE_FIELDS) && 'user' !== $key) {
                throw new \Exception("Invalid key in response: $key");
            }

            if ('user' !== $key) {
                $validator = self::VALID_SCORE_FIELDS[$key];
                $valid = ('isDate()' === $validator) ? $this->isDate($value) : preg_match($validator, $value);

                if (!$valid) {
                    throw new \Exception("Invalid score $key field value: $valid");
                }
            }

            $checkedScoreFields[] = $key;

            if ('user' !== $key) {
                continue;
            }

            foreach ($value as $userKey => $userField) {
                if (!array_key_exists($userKey, self::VALID_USER_FIELDS) && 'user' !== $value) {
                    throw new \Exception("Invalid key in response: $userKey");
                }

                $checkedUserFields[] = $userKey;

                $validator = self::VALID_USER_FIELDS[$userKey];
                $valid = preg_match($validator, $userField);

                if (!$valid) {
                    throw new \Exception("Invalid user $key field value: $valid");
                }
            }
        }

        if (count($checkedScoreFields) !== count(self::VALID_SCORE_FIELDS)) {
            throw new \Exception('Missing score fields');
        }

        if (count($checkedUserFields) !== count(self::VALID_USER_FIELDS)) {
            throw new \Exception('Missing user fields');
        }
    }

    private function isDate(string $string)
    {
        return (bool) strtotime($string);
    }
}
