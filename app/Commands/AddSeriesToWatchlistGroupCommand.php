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

use App\Models\WatchlistGroup;
use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputDefinition;

use App\Models\Watchlist;

/**
 * Class AddSeriesToWatchlistGroupCommand
 * @package App\Commands
 */
class AddSeriesToWatchlistGroupCommand extends Command
{

    /**
     * Configuration method
     */
    protected function configure()
    {
        $this
            ->setName('add:show')
            ->setDescription('Adds a show to watchlist group')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('group', 'g', InputOption::VALUE_REQUIRED),
                    new InputOption('show', 's', InputOption::VALUE_OPTIONAL),
                    new InputOption('link', 'l', InputOption::VALUE_OPTIONAL),
                ])
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $group = $input->getOption('group');
        $show = $input->getOption('show');
        $link = $input->getOption('link');
        if (!$show && !$link) {
            $output->writeln('<fg=red>You must provide a show ID or link</>');
            return;
        }

        //Let's fetch the ID
        if ($show && !$link) {

            if (!is_numeric($show) || intval($show) != $show || intval($show) <= 0) {
                $output->writeln('<fg=red>Error: Invalid ID</>');
                return;
            } else {
                $showId = intval($show);
            }

        } else {

            $link = $input->getOption('link');
            $url = parse_url($link);
            if (!$url || $url['host'] != 'www.tvmaze.com') {
                $output->writeln('<fg=red>Error: invalid URL</>');
                return;
            }

            preg_match("/\/shows\/([0-9]+)\//i", $url['path'], $matches);

            $showId = $matches[1];

        }

        $check = Watchlist::with('group')
            ->whereHas('group', function ($q) use ($group) {
                $q->where('title', $group);
            })
            ->where('tvmaze_id', $showId)
            ->first();

        if ($check) {
            $output->writeln('<fg=red>Error: The show you have provided already exists in the watchlist "' . $check->group->title . '"</>');
            return;
        }

        $groupData = WatchlistGroup::where('title', $group)->first();
        if (!$groupData) {
            $output->writeln('<fg=red>Error: Watchlist group not found</>');
            return;
        }

        $watchlist = new Watchlist();
        $watchlist->watchlist_group_id = $groupData->id;
        $watchlist->tvmaze_id = $showId;
        $watchlist->save();

        $output->writeln('<fg=green>Success: The show is successfully added to watchlist</>');

    }

}