lock '3.7.1'

set :application, 'Sirius'
set :repo_url, 'git@bitbucket.org:massmediagroup/sirius.git'
set :linked_files, %w{app/config/parameters.yml}
set :linked_dirs, %w{app/logs vendor web/vendor web/uploads node_modules web/bower_components web/css web/js web/img/banners web/img/products web/img/shares web/img/slider app/cache app/logs web/images web/media}

set :symfony_directory_structure, 2

set :format, :pretty
set :log_level, :debug
set :keep_releases, 2

after 'deploy:starting', 'composer:install_executable'
after 'deploy:updated', 'doctrine:migrate'
after 'deploy:updated', 'npm:install'
after 'deploy:updated', 'bower:install'
after 'deploy:updated', 'symfony:assets:install'
after 'deploy:updated', 'assetic:dump'
