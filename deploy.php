<?php
namespace Deployer;

require_once __DIR__ . '/vendor/deployer/deployer/recipe/magento2.php';

// Project name
set('application', 'Magento 2.4.0 - Local Dev');

set('release_name', function(){
    return date('YmdHis');
});

desc('Deploy assets');
task('magento:deploy:assets', function () {
    run("{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy -f");
});

host('web8.anasource.com')
    ->hostname('192.168.10.8')
    ->user('team3')
    ->configFile('~/.ssh/config')
    ->set('deploy_path', '/var/www/html/team3/vd_magento_2_4_0')
    ->stage('staging')
    ->roles('master');

// Project repository
set('repository', 'git@github.com:vishaltatva/magento_2_4_0.git');

set('default_stage', 'staging');

set('default_timeout', 3400);

set('user', 'team3');
//set('git_tty', true);
set('ssh_type', 'native');
set('ssh_multiplexing', false);
set('writable_mode', 'chmod');
//set('keep_releases', 10);


task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: $result");
});

task('test', function () {
    writeln(get('writable_mode'));
});


