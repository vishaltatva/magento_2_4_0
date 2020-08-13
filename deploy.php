<?php
namespace Deployer;

require __DIR__ . '/vendor/webgriffe/deployer-magento2/magento.php';

// Project name
set('application', 'Magento 2.4.0 - Local Dev');

set('release_name', function(){
    return date('YmdHis');
});

set('writable_dirs', [
    'var',
    'pub/static',
    'pub/media',
    'generated'
]);

set('deploy_mode', 'developer');
set('assets_locales', []);
set('assets_themes', []);

desc('Deploy assets');
task('magento:deploy:assets', function () {
    if (!is_magento_installed()) {
        return;
    }
    $timeout = 300;
    if (input()->hasOption(DEPLOY_ASSETS_TIMEOUT_OPTION_NAME)) {
        $timeout = input()->getOption(DEPLOY_ASSETS_TIMEOUT_OPTION_NAME);
    }
    $locales = implode(' ', get('assets_locales'));
    $themes = implode(
        ' ',
        array_map(
            function ($theme) {
                return '--theme=' . $theme;
            },
            get('assets_themes')
        )
    );
    run(
        "{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy -f $themes $locales",
        ['timeout' => $timeout]
    );
});

host('web8.anasource.com')
    ->hostname('192.168.10.8')
    ->user('team3')
    ->configFile('~/.ssh/config')
    ->set('deploy_path', '/var/www/html/team3/vd_magento_2_4_0')
    ->stage('staging')
    ->set('http_user', 'team3')
    ->set('http_group', 'www-data')
    ->set('base_url', 'https://web8.anasource.com/team3/vd_magento_2_4_0/current/')
    ->roles('master');

// Project repository
set('repository', 'git@github.com:vishaltatva/magento_2_4_0.git');

set('default_stage', 'staging');

set('default_timeout', 3400);

//set('git_tty', true);
set('ssh_type', 'native');
set('ssh_multiplexing', false);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '6777');
set('writable_use_sudo', true);
set('keep_releases', 1);

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'magento:mode:set',
    'magento:upgrade:db',
    'magento:compile',
    'magento:deploy:assets',
    'magento:cache:flush',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);
