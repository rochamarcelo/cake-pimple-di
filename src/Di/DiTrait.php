<?php

namespace RochaMarcelo\CakePimpleDi\Di;

/**
 * Provides an simple way to use the Di class
 */
trait DiTrait
{
    /**
     * Instance of the \RochaMarcelo\CakePimpleDi\Di\Di
     *
     * @var \RochaMarcelo\CakePimpleDi\Di\Di
     */
    protected $DiInstance = null;

    /**
     * Get RochaMarcelo\CakePimpleDi\Di\Di
     *
     * @return \RochaMarcelo\CakePimpleDi\Di\Di
     */
    public function di()
    {
        if ($this->DiInstance === null) {
            $this->DiInstance = Di::instance();
        }
        return $this->DiInstance;
    }
}
