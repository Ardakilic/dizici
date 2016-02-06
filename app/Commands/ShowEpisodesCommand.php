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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\Table;

use App\Models\Episode;
use App\Models\WatchlistGroup;

/**
 * Class ShowEpisodesCommand
 * @package App\Commands
 */
class ShowEpisodesCommand extends Command
{

    /**
     * Configuration method
     */
    protected function configure()
    {
        $this
            ->setName('list')
            ->setDescription('Show the episodes list of provided TVMaze show IDs')
            ->addArgument(
                'group',
                InputArgument::REQUIRED,
                'Name of the Watchlist Group'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string Output to console
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getArgument('group');
        if (!$group) {
            $output->writeln('<fg=red>Error: You have to provide the group name</>');
            return;
        }

        $watchListGroup = WatchlistGroup::with('watchlists')->where('title', $group)->first();
        if (!$watchListGroup) {
            $output->writeln('<fg=red>Error: No watchlist group found</>');
            return;
        }

        //Let's get the ID of of the shows
        $shows = $watchListGroup->watchlists->pluck('tvmaze_id')->toArray();

        //Now let's fetch episodes:
        $episodes = Episode::join('series', 'series.id', '=', 'episodes.serie_id_internal', 'inner')
            ->whereIn('series.external_id', $shows)
            ->orderBy('episodes.airdate', 'ASC')
            ->select([
                'episodes.id',
                'series.title as series_title', 'episodes.title',
                'episodes.season_id', 'episodes.episode_id', 'episodes.is_special',
                'episodes.url'
            ])
            ->get()
            ->toArray();

        $rows = array_map('array_values', $episodes);

        $table = new Table($output);
        $table
            ->setHeaders(array('Episode ID', 'Show', 'Title', 'Season', 'Episode', 'Is Special?', 'URL'))
            ->setRows($rows);

        $table->render();

    }


}