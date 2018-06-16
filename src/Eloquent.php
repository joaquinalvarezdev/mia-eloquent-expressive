<?php namespace Mobileia\Expressive\Database;

class Eloquent 
{
    /**
     * Inicializa la base de datos con Eloquent Lavarel
     */
    public static function install(\Psr\Container\ContainerInterface $container)
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection($container->get('config')['eloquent']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}