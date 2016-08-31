namespace :assetic do
  task :dump do
    on roles(:web) do
      symfony_console('assetic:dump', '-e=prod')
    end
  end
end