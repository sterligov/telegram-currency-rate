<?php


namespace App;


use App\Exception\UnregisteredContainerException;
use Psr\Container\ContainerInterface;

/**
 * @method static get(string $key) - return object instance from container
 *
 * Class Container
 * @package App
 */
class Container
{
    /**
     * @var null
     */
    private static $container = null;

    private function __construct()
    {
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws UnregisteredContainerException
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        if (!self::$container) {
            throw new UnregisteredContainerException('Container does not exist');
        }

        $method = new \ReflectionMethod(self::$container, $name);

        return $method->invoke(self::$container, ...$arguments);
    }

    public static function register(ContainerInterface $container)
    {
        self::$container = $container;
    }
}