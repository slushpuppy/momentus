<?php


namespace Ext\DesignPattern;


class Singleton
{
    // Hold the class instance.
    protected static $instance = null;

    // The constructor is private
    // to prevent initiation with outer code.
    protected function __construct()
    {
        // The expensive process (e.g.,db connection) goes here.
    }

    // The object is created from within the class itself
    // only if the class has no instance.
    /**
     * @return static
     */
    public static function I()
    {
        if (static::$instance == null)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }
}