
```
  ____  _     _      _
 |  _ \(_)___(_) ___(_)
 | | | | |_  / |/ __| |
 | |_| | |/ /| | (__| |
 |____/|_/___|_|\___|_|
```

# Dizici
Dizici (dee-zee-ghee) is a simple PHP Cli tool that syncs series and episodes from [TVMaze API](http://www.tvmaze.com/api) to a local database.

## Why?
Let me give you a brief example:

I'm a [Stargate](http://stargate.mgm.com/) fan, and it contains 3 TV shows and several movies. The "proper" watch order is a mess. You have to watch some episodes of one TV show, then switch to another to prevent spoiler and know all the details in order. There are even some [Reddit threads](https://www.reddit.com/comments/dllw8/the_official_rstargate_what_order_do_i_watch/) which discuss in which order the TV shows should be watched.

Now I'm watching [Doctor Who](http://www.bbc.co.uk/programmes/b006q2x0), and I'm reminded that it's also connected with [Torchwood](http://www.bbc.co.uk/programmes/b006m8ln), and the same issue is in this show, too.

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

## Requirements
- PHP 5.6.4 or newer
- [Composer](https://getcomposer.org)
- A database engine such as MySQL, Postgres, SQLite or SQL Server (which is supported by [illuminate/database](https://github.com/illuminate/database))
- Cron if you'd like to sync episodes automatically
- Recent version of cURL must be installed

## Installation (From Dist)
There are couple of ways to get the dist `.phar` file
- The easiest way is to get it from [NPM](https://www.npmjs.com/package/dizici):
- npm install -g dizici

or [Composer](https://getcomposer.org):

```shell
composer global require ardakilic/dizici
```

And it's installed.
- You can also manually download the latest stable version from [GitHub Releases page](https://github.com/Ardakilic/dizici/releases)
- Or you can get the bleeding edge dist version by cloning this repository, there'll be a `dist/dizici.phar` file available for you.

## Installation (From Source)
- Clone the repository:
- git clone [https://github.com/Ardakilic/dizici.git](https://github.com/Ardakilic/dizici.git)
- Install dependencies:
- cd dizici
composer install
- After first installation, a hidden folder called `.dizici` will be created inside your home folder. This folder is where the application stores configuration and database (if set as SQLite). We'll refer to it as `$HOME/.dizici/` in this readme file.
- Fill the credentials in `config.yml` accordingly. Example connection credentials are stored in [this file in Laravel](https://github.com/laravel/laravel/blob/becd774e049fb451aca0c7dc4f6d86d7bc12256c/config/database.php#L47). E.g: If you want to use MySQL instead, fill the connection key with [these keys and according values](https://github.com/laravel/laravel/blob/becd774e049fb451aca0c7dc4f6d86d7bc12256c/config/database.php#L56-L64).
- Create the tables on your database:
- dizici migrate:tables
- Now you need to create a "watchlist group":

>  "watchlist group" is a bucket of TV shows, a bucket can be called "Stargate Bundle", "Marvel Universe" (or anything you'd like), and it  contains TV shows such as "Stargate SG-1", "Stargate Atlantis", and "Stargate Universe". You can think it like an individual list or a shows group.

Simply run this command:

```shell
dizici create:group -t "Stargate List"
```

or alternatively:

```shell
dizici create:group --title="Stargate List"
```

- Now you need to add TV shows to a watchlist, you can do this with either show ID, or the link directly:

```shell
dizici add:show -g "Stargate List" -s 204
```

or this:

```shell
dizici add:show --group="Stargate List" --show=204
```

`group` is the title of our Watchlist Group. `show` is the ID of the show ID of TVMaze. You can get it from the URL. e.g: `http://www.tvmaze.com/shows/204/stargate-sg-1`

You can also add a show by URL directly, Dizici will take care of the rest.

```shell
diziciadd:show -g "Stargate List" -l http://www.tvmaze.com/shows/204/stargate-sg-1
```

or

```shell
dizici add:show --group="Stargate List" --link=http://www.tvmaze.com/shows/204/stargate-sg-1
```

Repeat this step for each TV show you'd like to add to a Watchlist Group.
- Now sync all the series and episodes, some example TV shows are already added in configuration file:
- dizici sync
- Optionally, add the command to your crontab to automatically sync in a period you've set.
- Enjoy! :smile:

## Building the Binary
There should already be a `dist/dizici.phar` file available in the repository, but for some purposes, you may want to create the .phar file on your own.

With a very little and simple steps you can create `dizici.phar` file yourself.
- [Download and/or install the Box2](https://github.com/box-project/box2#as-a-phar-recommended)
- cd into the `dizici`'s directory
- Make sure you've installed dependencies with `composer install`
- Run `php box.phar build`
- You'll have a `dist/dizici.phar` created upon seconds.

## Install Dizici Globally
### You can do this easily with NPM:

```shell
npm install -g dizici
```

or via Composer:

```shell
composer global require ardakilic/dizici
```

And it's installed :)

### You can also download the .phar arcive and do this manually
- First, either download the dist version or [build the binary](#building-the-binary). You can download or build yourself.
- Move `dizici.phar` to one of your ENV paths. Example:
- sudo mv dizici.phar /usr/local/bin/dizici
- Finally, you can run `dizici` from anywhere in your terminal.

## Listing TV shows as unified
There's a cli way to show and export this feature.

First, make sure you're synced,

```shell
dizici sync
```

Then run this command:

```shell
dizici episodes Stargate
```

or with quotes if it contains multiple words:

```shell
dizici episodes "Stargate List"
```

You'll get an output like this:

![](https://i.imgur.com/zQa4IxQ.png)

If you want to export this, you can do this the shell way:

```shell
dizici episodes "Stargate List" > output.txt
```

and print `output.txt` etc.

## Screenshot(s)
This is a sample screenshot from console when you run the sync command:

![imgur](https://i.imgur.com/8nNjHSX.png)

Many of the other images are provided earlier of this readme.

## TODOs
- ~~Grouping feature to bundle multiple TV shows~~
- ~~Storing all TVMaze IDs in database instead of config file~~
- ~~[Tables](http://symfony.com/doc/current/components/console/helpers/table.html) in console output~~
- Provide output such as HTML, tsv etc. in addition to text
- New columns for marking such as "watched", "collected" etc.
- Please feel free to provide issues and pull requests. I'll gladly consider them.

## Version History
### Version 1.1.0
- Watchlist groups: You can now create and name custom watch lists, and create lists like "Shows for Summer", "Marvel Universe" etc, add TV shows to these lists and and call the episodes lists by these names. This allows you both to call the command easier, and manage better. This resulted the deletion of `series` key in `config.yml`.
- New commands to create watchlist groups and add TV shows to these groups.

### Version 1.0.1
- NPM submission for easier installation

### Version 1.0.0
This release aims to make Dizici as portable as possible.
- SQLite is now the default connection
- Configuration file is now [YAML](http://www.yaml.org/). The `.yml` file is parsed using [Symfony Yaml Component](http://symfony.com/doc/current/components/yaml/introduction.html)
- Standalone .phar archive created, using [Box-project's Box2](http://box-project.github.io/box2/)
- Configuration path is now `$HOME/.dizici/` . This way, the app's aimed to be one single archive, and more portable

### Version 0.2.0
- Cli app renamed to `dizici` from `series`
- Cli table output implemented

### Version 0.1.0
- First release

## License
MIT License
