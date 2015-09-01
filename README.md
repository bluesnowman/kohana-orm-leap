# Leap for PHP

Leap is a database management library for PHP.  It was originally developed to be used in conjunction with the
[Kohana PHP Framework](http://kohanaframework.org), but has since evolved and been adapted to work with any PHP
framework.  It is designed to work with DB2, Drizzle, Firebird, MariaDB, MS SQL, MySQL, Oracle, PostgreSQL, and
SQLite.  Leap acts as a common interface between the different  database dialects and connections.  It provides
a powerful query builder and ORM.  Leap's ORM is based on the active record design pattern, which utilizes PHP
objects to model database tables.

Make sure to "Star" this project if you like it.

## Motivation

Leap is meant to be a simple, clean project.  The primary goal of Leap was to create an ORM for the Kohana PHP
Framework that works with all major databases.  Implementing Leap is ease and generally more intuitive than other
ORMs like [Doctrine](http://www.doctrine-project.org/projects/orm).  A goal for the development of Leap was to
create an ORM that can harness the power of  composite keys, which many other ORMs (e.g. [Kohana's official ORM](https://github.com/kohana/orm), [Jelly](https://github.com/creatoro/jelly),
and [Sprig](https://github.com/sittercity/sprig/)) cannot handle.

## Features

Leap provides a number of features, such as:

* Plugins for DB2, Drizzle, Firebird, MariaDB, MS SQL, MySQL, Oracle, PostgreSQL, and SQLite.
* Designed to work in conjunction with other database tools for Kohana.
* [Config file for designating the database driver (e.g. PDO) and connection strings](http://spadefoot.github.io/kohana-orm-leap/tutorials/setting-up-a-database-connection/).
* Classes are easily extensible.
* A [database connection pool](http://spadefoot.github.io/kohana-orm-leap/tutorials/establishing-a-database-connection/) for managing resources.
* A powerful [query builder for creating SQL statements](http://spadefoot.github.io/kohana-orm-leap/tutorials/building-sql-statements/).
* Sanitizes data to help prevent SQL injection attacks.
* Capable of handling non-integers primary keys.
* Supports composite primary keys and composite foreign keys.
* Enforces strong data types on [database fields](http://spadefoot.github.io/kohana-orm-leap/tutorials/mapping-a-model/#fields).
* Allows [field aliases](http://spadefoot.github.io/kohana-orm-leap/tutorials/mapping-a-model/#aliases) to be declared.
* Makes working with certain database fields easy with [field adaptors](http://spadefoot.github.io/kohana-orm-leap/tutorials/mapping-a-model/#adaptors).
* A set of Auth classes for authenticating user logins.
* A toolkit of useful functions.
* Lots of [tutorials](http://spadefoot.github.io/kohana-orm-leap/tutorials/).

## Getting Started (using Kohana)

To start using Leap, follow these steps:

1. Just download the module (see below regarding as to which branch) from github.
2. Unzip the download to the modules folder in Kohana.
3. Rename the uncompressed folder to "leap".
4. Modify leap/config/database.php.
5. Add "leap" as a module to application/bootstrap.php.
6. Begin creating your models in the application/classes/model/leap/ folder.

For more information, see the tutorial on [installing Leap](http://spadefoot.github.io/kohana-orm-leap/install/).

## Required Files

The Leap ORM module is meant to be a completely independent module.  As for the files within Leap, you can remove
any database plugins that you are not using.

## Documentation

This project is accompanied by [a companion Web site](http://spadefoot.github.io/kohana-orm-leap/), which documents
the API for the Leap ORM and has a number of [examples and tutorials](http://spadefoot.github.io/kohana-orm-leap/tutorials/).
You can also find other tutorials and examples online (please let us know if you find one that we should highlight
here).

## Further Assistance

Although Leap is simple to use with any PHP Framework, you can get further assistance by asking questions on either [Kohana's Forum](http://forum.kohanaframework.org/)
or [Stack Overflow](http://stackoverlow.com).

## Reporting Bugs & Making Recommendations

If you find a bug in the code or if you would like to make a recommendation, we would be happy to hear from you.  Here are three methods
you can use to notify us:

* Log an issue in this project's [issue tracker](https://github.com/spadefoot/kohana-orm-leap/issues).
* Create a fork of this project and submit a [pull request](http://help.github.com/send-pull-requests/).
* Send an email to spadefoot.oss@gmail.com.

## Known Issues

Please see this project's [issue tracker](https://github.com/bluesnowman/leap/issues) on github for any known
issues.

## Updates

Make sure that you add yourself as a watcher of this project so that you can watch for updates.

## Future Development

This project is constantly being improved and extended. If you would like to contribute to Leap, please fork the
project and then send us your additions/modifications using a [pull request](http://help.github.com/send-pull-requests/).

## License

### Apache v2.0

Copyright © 2011–2015 Spadefoot Team.

Unless otherwise noted, Leap is licensed under the Apache License, Version 2.0 (the "License"); you may not use these files except in
compliance with the License. You may obtain a copy of the License at:

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions
and limitations under the License.
