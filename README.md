sirius
======

A Symfony project created on February 26, 2016, 4:13 pm.

##Installation
* run `composer install`
* run `npm install`
* create compass.yml in /app/config/ with "parameters: assetic.filter.compass.bin: %your_path_to_compass%"
* run `php app/console assets:install --symlink web/`
* run `php app/console assetic:dump` to compile assets, or `php app/console assetic:watch` to watch and recompile files on changes
