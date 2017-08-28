<?php

namespace RochaMarcelo\CakePimpleDi\Di;

use Pimple\Container;

/**
 * Dependency injection based on silexphp/Pimple
 *
 * @author  Marcelo Rocha <contato@omarcelo.com.br>
 */
class Di
{
    const TYPE_FACTORY = 'factory';

    const TYPE_PARAMETER = 'parameter';

    const TYPE_DEFAULT = 'default';

    /**
     * Container
     *
     * @var Pimple\Container
     */
    protected $container;

    /**
     * Construtor
     *
     * @param Container $container Container de dependencia
     */
    public function __construct(Container $container = null)
    {
        if ($container === null) {
            $container = new Container;
        }
        $this->container = $container;
    }
    /**
     * Get static instance of this class
     *
     * @param string $name     Instance identifier name
     * @param bool   $forceNew Should ignore old instance and force new one
     * @return Di
     */
    public static function instance($name = null, $forceNew = false)
    {
        static $instance;
        if ($name === null) {
            $name = 'default';
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string value');
        }

        if (!isset($instance[$name]) || $forceNew === true) {
            $instance[$name] = new static();
        }

        return $instance[$name];
    }

    /**
     * Gets the container.
     *
     * @return Pimple\Container
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * Sets a parameter or an object.
     *
     * @param string $id    The unique identifier for the parameter or object
     * @param mixed  $value The value of the parameter or a closure to define an object
     * @param string $type  The "service" type, factory, parameters or default
     *
     * @throws \RuntimeException Prevent override of a frozen service
     * @return void
     */
    public function set($id, $value, $type = null)
    {
        if ($type === static::TYPE_PARAMETER && is_object($value) && method_exists($value, '__invoke')) {
            $value = $this->container()->protect($value);
        }

        if ($type === static::TYPE_FACTORY) {
            $value = $this->container[$id] = $this->container()->factory($value);
        }

        $this->container[$id] = $value;

    }

    /**
     * Sets a parameter or an object.
     *
     * @param array $services List of services to set
     *
     * @return void
     */
    public function setMany(array $services)
    {
        foreach ($services as $id => $service) {
            if (!is_array($service)) {
                $this->set($id, $service);
                continue;
            }

            if (!isset($service['id'])) {
                $service['id'] = $id;
            }
            if (!array_key_exists('value', $service)) {
                throw new \InvalidArgumentException(
                    'Value for "' . $service['id'] . '" must be provided'
                );
            }

            $type = null;
            if (isset($service['type'])) {
                $type = $service['type'];
            }
            $this->set($service['id'], $service['value'], $type);
        }
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or an object
     *
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    public function get($id)
    {
        return $this->container[$id];
    }

    /**
     * Handles containers methodd
     *
     * @param string $method name of the container method to be invoked
     * @param array  $args   List of arguments passed to the function
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->container, $method],
            $args
        );
    }
}
