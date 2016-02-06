<?php

/**
 * Dizici
 * https://github.com/Ardakilic/dizici
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link        https://github.com/Ardakilic/dizici
 * @copyright   2016 Arda Kilicdagi. (https://arda.pw/)
 * @license     http://opensource.org/licenses/MIT - MIT License
 */

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputDefinition;

use App\Models\WatchlistGroup;

/**
 * Class CreateWatchlistGroupCommand
 * @package App\Commands
 */
class CreateWatchlistGroupCommand extends Command
{

    /**
     * Configuration method
     */
    protected function configure()
    {
        $this
            ->setName('create:group')
            ->setDescription('Creates a watchlist group with a provided name')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('title', 't', InputOption::VALUE_REQUIRED),
                ])
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $group = $input->getOption('title');
        if (!$group) {
            $output->writeln('<fg=red>You must provide a title for your command</>');
            return;
        }

        $check = WatchlistGroup::where('title', $group)->first();
        if ($check) {
            $output->writeln('<fg=yellow>A group with this name already exists</>');
            return;
        }

        $watchListGroup = new WatchlistGroup();
        $watchListGroup->title = $group;
        $watchListGroup->save();

        $output->writeln('<fg=green>Success! Group with the title of ' . $group . ' created successfully!</>');

    }

}