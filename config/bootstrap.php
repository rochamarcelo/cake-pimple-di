<?php
use Cake\Core\Configure;
use RochaMarcelo\CakePimpleDi\Di\Di;

$config = Configure::consume('CakePimpleDi');
$scopes = [];
if ( !isset($config['scopes']) ) {
    $scopes = [
        'default' => (array)$config
    ];
}

foreach ( $scopes as $name => $config ) {
    $Di = Di::instance($name);

    $config = $config + [
        'providers' => []
    ];

    foreach ($config['providers'] as $provider ) {
        if ( is_string($provider) ) {
            $provider = new $provider;
        }

        $Di->register($provider);
    }

    if ( isset($config['services']) && is_array($config['services']) ) {
        $Di->setMany($config['services']);
    }
}
if (!empty($config['actionInjections'])) {
    \Cake\Event\EventManager::instance()->on(new \RochaMarcelo\CakePimpleDi\Event\ActionInjectionListener($config['actionInjections']));
}

if (isset($config['useRequest']) && $config['useRequest']) {
    \Cake\Event\EventManager::instance()->on(new \RochaMarcelo\CakePimpleDi\Event\ContainerDispatchListener());
}
