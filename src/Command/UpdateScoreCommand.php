<?php

namespace App\Command;

use App\Factory\ScoreFactory;
use App\Service\ScoreVendorClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateScoreCommand extends Command
{
    /**
     * @var ScoreFactory
     */
    private $scoreFactory;

    /**
     * @var ScoreVendorClient
     */
    private $client;

    /**
     * @required
     * @param ScoreFactory $scoreFactory
     */
    public function setScoreFactory(ScoreFactory $scoreFactory)
    {
        $this->scoreFactory = $scoreFactory;
    }

    /**
     * @required
     * @param ScoreVendorClient $scoreVendorClient
     */
    public function setClient(ScoreVendorClient $scoreVendorClient)
    {
        $this->client = $scoreVendorClient;
    }

    protected function configure()
    {
        $this
            ->setName('app:update:score')
            ->setDescription('Update score');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $scores = $this->client->getScore();
            $this->scoreFactory->build($scores);

            $io->success('Scores successfully updated!');
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
        }
    }
}
