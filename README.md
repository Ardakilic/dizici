```
  ____  _     _      _
 |  _ \(_)___(_) ___(_)
 | | | | |_  / |/ __| |
 | |_| | |/ /| | (__| |
 |____/|_/___|_|\___|_|
```

#Dizici

Dizici (dee-zee-ghee) is a simple PHP Cli tool that syncs series and episodes from [TVMaze API](http://www.tvmaze.com/api) to a local database.

##Why?

Let me give you a brief example:

I'm a [Stargate](http://stargate.mgm.com/) fan, and it contains 3 TV showsand several movies. The "proper" watch order is a mess. You have to watch some episodes of one TV show, then switch to another to prevent spoiler and know all the details in order. There are even some [Reddit threads](https://www.reddit.com/comments/dllw8/the_official_rstargate_what_order_do_i_watch/) which discuss in which order the serie should be watched.

Now I'm watching [Doctor Who](http://www.bbc.co.uk/programmes/b006q2x0), and I'm reminded that it's also conncted with [Torchwood](http://www.bbc.co.uk/programmes/b006m8ln), and the same issue is in this show, too.

So in short, I needed a system that'd show me a sum of unified series that are scenarically connected and sorted such as this:

This is the watch order of Stargate Series, which includes movies and 3 TV shows.
```
* 01 - Stargate movie
* 02 - Stargate SG-1, episodes 1.1 to 8.2
* 03 - Stargate Atlantis, episodes 1.1 to 1.15
* 04 - Stargate SG-1, episodes 8.3 to 8.20
* 05 - Stargate Atlantis, episodes 1.16 to 2.1
* 06 - Stargate SG-1, episodes 9.1 to 10.2
* 07 - Stargate Atlantis, episodes 2.2 to 3.4
* 08 - Stargate SG-1, episodes 10.3 to 10.12
* 09 - Stargate Atlantis, episodes 3.5 to 3.19
* 10 - Stargate SG-1, episodes 10.13 to 10.20
* 11 - Stargate: The Ark of Truth
* 12 - Stargate Atlantis, episodes 3.20 to 5.1
* 13 - Stargate: Continuum
* 14 - Stargate Atlantis, episodes 5.2 onwards.
* 15 - Stargate Universe, All
```

I couldn't find such a service that provides this (show more than one TV show, sort the episodes by air date, list them as unified, and make a list).

This simple PHP cli tool aims to be a solution for this issue.

##Requirements

* PHP 5.5.9 or newer
* [Composer](https://getcomposer.org)
* A database engine such as MySQL, Postgres, SQLite or SQL Server (which is supported by [illuminate/database](https://github.com/illuminate/database))
* Cron if you'd like to sync episodes automatically
* Recent version of cURL must be installed

##Installation (From Dist)

There are couple of ways to get the dist .phar file
* The easiest way is to get it from [NPM](https://www.npmjs.com/package/dizici):
```shell
npm install -g dizici
```

or [Composer](https://getcomposer.org):

```shell
composer global require Ardakilic/dizici
```

And it's installed.
* You can also manually download the latest stable version from [GitHub Releases page](https://github.com/Ardakilic/dizici/releases)
* Or you can get the bleeding edge dist version by cloning this repository, there'll be a `dist/dizici.phar` file available for you.

##Installation (From Source)

* Clone the repository:
```shell
git clone https://github.com/Ardakilic/dizici.git
```
* Install dependencies:
```shell
cd dizici
composer install
```
* After first installation, a hidden folder called `.dizici` will be created inside your home folder. This folder is where the application stores configuration and database (if set as SQLite). We'll refer to it as `$HOME/.dizici/` in this readme file.
* Fill the credentials in `config.yml` accordingly. Example connection credentials are stored in [this file in Laravel](https://github.com/laravel/laravel/blob/becd774e049fb451aca0c7dc4f6d86d7bc12256c/config/database.php). E.g: If you want to use MySQL instead, fill the connection key with [these keys and according values](https://github.com/laravel/laravel/blob/becd774e049fb451aca0c7dc4f6d86d7bc12256c/config/database.php#L56-L64).
* Create the tables on your database:
```shell
php bin/dizici migrate:tables
```
* Now sync all the series and episodes, some example TV shows are already added in configuration file:
```shell
php bin/dizici sync:series
```
* Optionally, add the command to your crontab to automatically sync in a period you've set.
* Enjoy! :smile:

##Building the Binary

There should already be a `dist/dizici.phar` file available in the repository, but for some purposes, you may want to create the .phar file on your own.

With a very little and simple steps you can creeate `dizici.phar` file yourself.

* [Download and/or install the Box2](https://github.com/box-project/box2#as-a-phar-recommended)
* cd into the dizici's directory
* Make sure you've installed dependencies with `composer install`
* Run `php box.phar build`
* You'll have a `dist/dizici.phar` created upon seconds.

##Install Dizici Globally

###You can do this easily with NPM:

```shell
npm install -g dizici
```

or via Composer:

```shell
composer global require Ardakilic/dizici
```

And it's installed :)

###You can also download the .phar arcive and do this manually

* First, either download the dist version or [build the binary](#building-the-binary). You can download or build yourself.
* Move `dizici.phar` to one of your ENV paths. Example:
```shell
sudo mv dizici.phar /usr/local/bin/dizici
```
* Finally, you can run `dizici` from anywhere in your terminal.

##Adding new TV Shows

This is quite easy, I'll try to explain in some simple steps:

* Navigate to [http://www.tvmaze.com/](http://www.tvmaze.com/), and search for a TV show
* Search for a TV show, let's search Star Trek, there are various results, I'll post some of them here:
![](https://i.imgur.com/hLt9dtQ.png)
* The links are like these: http://www.tvmaze.com/shows/ **490** /star-trek, http://www.tvmaze.com/shows/ **491** /star-trek-the-next-generation http://www.tvmaze.com/shows/ **492** /star-trek-voyager,
* As you've realized, we need the TVMaze IDs of these shows, which are **490**, **491** and **492** (and so on).
* Just add these numbers in `series` key in `config.yml` which can be found under `$HOME/.dizici/` directory.

##Listing TV shows as unified
There's a cli way to show and export this feature.

First, make sure you're synced,

```shell
php bin/dizici sync:series
```

Then run this command:

```shell
php bin/dizici show:episodes TVMazeShowID1 TVMazeShowID2..
```

You'll get an output like this:

![](https://i.imgur.com/zQa4IxQ.png)

If you want to export this, you can do this the shell way:

```shell
php dizici show:episodes TVMazeShowID1 TVMazeShowID2 > output.txt
```

and print output.txt etc.

Additionally, you can always run raw SQL queries, Not all the fields (such as image etc.) are shown in the table yet, and this may be one of the ways to implement it.

Example query that lists and provides a watch order for Doctor Who and Torchwood in MySQL (and derivatives):

```sql
SELECT * FROM `episodes` WHERE serie_id_external IN (210, 659) ORDER BY airdate ASC
```

(Again: 210 and 659 are TVMaze IDs which I've described how to get them earlier)

You will get a result like [this image](https://imgur.com/nW2rn5Z). This will include a unified view of multiple TV shows including special editions, episode names and numbers, summaries, cover photos, episode URLs and air dates (in short, whatever resource TVMaze provides and the application saves).

##Screenshot(s)

This is a sample screenshot from console when you run the sync command:

![imgur](https://i.imgur.com/8nNjHSX.png)

Many of the other images are provided earlier of this readme.

##TODOs
* Grouping feature to bundle multiple TV shows
* Storing all TVMaze IDs in database instead of config file
* ~~[Tables](http://symfony.com/doc/current/components/console/helpers/table.html) in console output~~
* Provide output such as HTML, tsv etc. in addition to text
* New columns for marking such as "watched", "collected" etc.
* Please feel free to provide issues and pull requests. I'll gladly consider them.


##Version History

###Version 1.0.1

* NPM submission for easier installation

###Version 1.0.0

This release aims to make Dizici as portable as possible.

* SQLite is now the default connection
* Configuration file is now [YAML](http://www.yaml.org/). The `.yml` file is parsed using [Symfony Yaml Component](http://symfony.com/doc/current/components/yaml/introduction.html)
* Standalone .phar arcive created, using [Box-project's Box2](http://box-project.github.io/box2/)
* Configuration path is now `$HOME/.dizici/` . This way, the app's aimed to be one single archive, and more portable

###Version 0.2.0

* Cli app renamed to `dizici` from `series`
* Cli table output implemented

###Version 0.1.0

* First release

##License
MIT License