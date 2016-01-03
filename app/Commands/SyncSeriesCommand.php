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
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\Models\Episode;
use App\Models\Serie;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class SyncSeriesCommand
 * @package App\Commands
 */
class SyncSeriesCommand extends Command
{
    //NOTE think about using pimple if such injections are required more in the future
    private $config;
    private $client;
    const separator = '-----------------------------------------------------------------';


    public function __construct()
    {
        parent::__construct();
        global $config; //TODO a better way, pimple is overkill for this stage in my opinion
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('sync:series')
            ->setDescription('Gets all the series from the config file, and syncs to the database accordingly');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Syncing whole of the series according to the configuration file');
        $this->syncSeriesAndEpisodes($input, $output);
        $output->writeln('Done! Series and episodes are synced successfully!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function syncSeriesAndEpisodes(InputInterface $input, OutputInterface $output)
    {
        //THE LOGIC: CREATE ONLY THE NON-EXISTING SERIES, BUT LOOP ALL EPISODES THROUGH API
        $output->writeln('Syncing the series from TVMaze to Database...');
        $this->syncSeries($input, $output);
        $output->writeln(self::separator);
        $output->writeln('Done! Now syncinc episodes...');
        $this->syncEpisodes($input, $output);
    }


    /**
     * Creates the new series
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function syncSeries(InputInterface $input, OutputInterface $output)
    {


        $allSeries = $this->config['series'];
        $currentSeries = Serie::all()->lists('external_id')->toArray();
        $seriesToCreate = array_values(array_diff($allSeries, $currentSeries));

        //Loop through all series to create
        foreach ($seriesToCreate as $serieID) {
            //First let's fetch from TVMaze:
            $request = new Request('GET', 'http://api.tvmaze.com/shows/' . $serieID);
            $response = $this->client->send($request);
            if ($response->getStatusCode() != 200) {
                $output->writeln('An error has been occurred while fetching series with ID ' . $serieID);
                $output->writeln('Error code: ' . $response->getStatusCode());
                $output->writeln(self::separator);
                continue;
            }
            $data = json_decode($response->getBody()->getContents(), true);

            //Now let's create the serie!
            $serie = new Serie;
            $serie->title = $data['name'];
            $serie->external_id = $data['id'];
            if (isset($data['image']['original'])) {
                $serie->image = $data['image']['original'];
            }
            $serie->premiered = date('Y-m-d H:i:s', strtotime($data['premiered']));
            $serie->save();

            $output->writeln($serie->title . ' created successfully!');
            $output->writeln(self::separator);
        }
    }

    //
    /**
     * Creates new episodes
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function syncEpisodes(InputInterface $input, OutputInterface $output)
    {

        //Each episode has an unique ID in TVMaze, this may be a good hint for us
        //First, let's fetch all series with keys as external IDS, we'll loop through them
        $currentSeries = Serie::all()->keyBy('external_id')->toArray();

        //FirstOrNew is overkill, "eager load Series + contains($episode_id)" cannot check by external_id column, so pre-fetching is best approach
        $currentEpisodes = Episode::all()->lists('title', 'external_id')->toArray();

        foreach ($currentSeries as $external_id => $serie) {

            $output->writeln('Now syncing: ' . $serie['title']);

            $request = new Request('GET', 'http://api.tvmaze.com/shows/' . $external_id . '/episodes?specials=1');
            $response = $this->client->send($request);
            if ($response->getStatusCode() != 200) {
                $output->writeln('An error has been occurred while fetching series with external_id ' . $external_id);
                $output->writeln('Error code: ' . $response->getStatusCode());
                $output->writeln(self::separator);
                continue;
            }
            $episodes = json_decode($response->getBody()->getContents(), true);

            //Loop through all episodes:
            foreach ($episodes as $data) {

                if (!isset($currentEpisodes[$data['id']])) {
                    $episode = new Episode();

                    $episode->external_id = $data['id'];

                    $episode->serie_id_external = $external_id;
                    $episode->serie_id_internal = $currentSeries[$external_id]['id'];

                    $episode->season_id = $data['season'];

                    //Episode ID can be null or an integer, it MOST POSSIBLY IS special
                    if (is_null($data['number'])) {
                        $episode->episode_id = 0;
                        $episode->is_special = 1;
                    } else {
                        $episode->episode_id = $data['number'];
                        $episode->is_special = 0;
                    }

                    $episode->title = $data['name'];
                    $episode->description = $data['summary'];

                    $episode->url = $data['url'];

                    if (isset($data['image']['original'])) {
                        $episode->image = $data['image']['original'];
                    }

                    $episode->airdate = $data['airdate'] . ' ' . $data['airtime'];

                    $episode->save();
                }

            }


            $output->writeln($serie['title'] . ' synced successfully!');
            $output->writeln(self::separator);
        }

    }
}