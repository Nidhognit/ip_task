<?php
/**
 * Created by PhpStorm.
 * User: nidhognit
 * Date: 19.12.17
 * Time: 16:50
 */

namespace VirgoIpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IpAddCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('ip:add')
            ->setDescription('Add ip')
            ->addArgument('ip', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = $input->getArgument('ip');
        $driverProvider = $this->getContainer()->get('driver.provider');
        $driverProvider->validateIp($ip);
        $count = $driverProvider->addIp($ip);

        $output->writeln('count: ' . $count);
    }
}