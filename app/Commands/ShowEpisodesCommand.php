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
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Helper\Table;

use App\Models\Episode;

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
            ->setName('show:episodes')
            ->setDescription('Show the episodes list of provided TVMaze show IDs')
            ->addArgument(
                'shows',
                InputArgument::IS_ARRAY,
                'Which shows do you want to get episodes list of?'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shows = $input->getArgument('shows');
        if (!$shows) {
            $output->writeln('You have to provide at least one TVMaze show ID');
            return;
        }

        //Let's convert them to integer:
        $shows = array_map('intval', $shows);

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