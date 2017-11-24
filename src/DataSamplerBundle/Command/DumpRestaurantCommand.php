<?php

namespace DataSamplerBundle\Command;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpRestaurantCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('datasampler:dump-restaurant')
            ->setDescription('...')
            ->addArgument('id_restaurant', InputArgument::REQUIRED, 'Restaurant ID')
            // ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $idRestaurant = $input->getArgument('id_restaurant');
        $output->writeln('Restaurant ID: '.$idRestaurant);

        $result = $this->executeQuery('SELECT * FROM restaurant where restaurant.id_restaurant = '.$idRestaurant.';');
        var_dump($result);

        $query = 'SELECT COUNT(*) FROM reservation WHERE reservation.id_restaurant = '.$idRestaurant.';';
        // $result = $this->executeQuery($query);
        $result = $this->executeQuery($query, $this->connectToShard(0));
        var_dump($result);
    }

    private function executeQuery($query, $connection = null)
    {
        if (null === $connection) {
            $connection = $this->getContainer()->get('doctrine')->getEntityManager()->getConnection();
        }

        $statement = $connection->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    private function connectToShard($shard)
    {
        $params = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'user' => 'lafourchette',
            'password' => 'lafourchette',
            'dbname' => 'rr_' . $shard
        ];

        $connection = $this->getContainer()->get('doctrine.dbal.connection_factory')
            ->createConnection($params);
        var_dump($connection);

        return $connection;
    }
}
