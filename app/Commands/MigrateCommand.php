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

/**
 * Class MigrateCommand
 * @package App\Commands
 */
class MigrateCommand extends Command
{
    /**
     * Configuration method
     */
    protected function configure()
    {
        $this
            ->setName('migrate:tables')
            ->setDescription('Creates the tables to database for the system');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Installing the tables to your database...');

        $this->migrateSchema();

        $output->writeln('Done! Database tables installed successfully!');
    }


    /**
     * Runs the schema builder commands
     */
    private function migrateSchema()
    {
        $this->createSeriesTable();
        $this->createEpisodesTable();
        $this->createWatchlistGroupsTable();
        $this->createWatchListTable();
    }

    /**
     * Creates the series table
     */
    private function createSeriesTable()
    {
        Capsule::schema()->create('series', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('external_id')->unsigned()->unique()->index(); //the ID on TVMaze

            $table->string('title');

            $table->string('image', 400)->nullable();

            $table->datetime('premiered');
        });
    }

    /**
     * Creates the episodes table
     */
    private function createEpisodesTable()
    {
        Capsule::schema()->create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('external_id')->unsigned()->unique()->index();

            $table->integer('serie_id_internal')->unsigned()->index();
            $table->integer('serie_id_external')->unsigned()->index();

            $table->integer('season_id')->unsigned()->index();
            $table->integer('episode_id')->unsigned()->nullable()->index();
            $table->enum('is_special', [0, 1])->default(0)->index(); //It is special if episode_id is null, may not be required

            $table->string('title');
            $table->text('description');

            $table->string('url', 400);

            $table->string('image', 400)->nullable();

            $table->datetime('airdate');
        });
    }

    /**
     * Creates the watchlist groups table
     */
    private function createWatchlistGroupsTable()
    {
        Capsule::schema()->create('watchlist_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });
    }

    private function createWatchListTable()
    {
        Capsule::schema()->create('watchlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('watchlist_group_id')->unsigned()->index();
            $table->integer('tvmaze_id')->unsigned()->index();
        });
    }

}