namespace :doctrine do
  task :migrate do
    on roles(:db) do
      symfony_console('doctrine:migrations:migrate', '--no-interaction')
    end
  end
end