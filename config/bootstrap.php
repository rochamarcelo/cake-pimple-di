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

    if ( isset($config['services']) && is_array($config['services']) ) {
        $Di->setMany($config['services']);
    }

    foreach ($config['providers'] as $provider ) {
        if ( is_string($provider) ) {
            $provider = new $provider;
        }

        $Di->register($provider);
    }
}