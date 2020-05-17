<?php
/**
 * Class PingCommand
 *
 * @author Akim Maksimov <a.maksimov@artox.com>
 */
declare(strict_types=1);

namespace ArtoxLab\Bundle\ClarcCodeGeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends Command
{
    protected static $defaultName = 'generator:entity';

    protected function configure()
    {
        $this
            ->setDescription('Entity code generator')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('domain', null, InputOption::VALUE_REQUIRED),
                    new InputOption('entity', null, InputOption::VALUE_REQUIRED),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        print_r($input->getArguments());
        die;
    }
}