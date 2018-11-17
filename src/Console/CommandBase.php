<?php

namespace Aedart\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console Command Base
 *
 * <br />
 *
 * Abstraction for console commands.
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Console
 */
abstract class CommandBase extends Command
{
    /**
     * This command's input
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * This command's output
     *
     * @var OutputInterface|StyleInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Set input
        $this->input = $input;

        // Resolve output
        if($output instanceof StyleInterface){
            $this->output = $output;
        } else {
            $this->output = new SymfonyStyle($this->input, $output);
        }

        // Finally, run the command
        return $this->runCommand();
    }

    /*****************************************************************
     * Abstract Methods
     ****************************************************************/

    /**
     * Execute this command
     *
     * @return int|null
     */
    abstract public function runCommand() : ?int ;
}
