<?php

/*
 * This file is part of the WordPress Helthe Monitor plugin.
 *
 * (c) Carl Alexander <carlalexander@helthe.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A timer for tracking the time it takes WordPress to do things.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Timer
{
    /**
     * The start time.
     *
     * @var float
     */
    private $start_time;

    /**
     * Flag that tracks if the timer is started.
     *
     * @var bool
     */
    private $started;

    /**
     * The stop time.
     *
     * @var float
     */
    private $stop_time;

    /**
     * Constructor.
     *
     * @param bool $start
     */
    public function __construct($start = true)
    {
        $this->reset();

        if ($start) {
            $this->start();
        }
    }

    /**
     * Get the time on the timer. If the timer is started, it will return the time elapsed since the timer started.
     *
     * @return float
     */
    public function get_time()
    {
        $stop_time = $this->stop_time;

        if ($this->started) {
            $stop_time = microtime(true);
        }

        return $stop_time - $this->start_time;
    }

    /**
     * Resets the timer.
     */
    public function reset()
    {
        $this->start_time = 0;
        $this->stop_time = 0;
        $this->started = false;
    }

    /**
     * Starts the timer.
     */
    public function start()
    {
        if ($this->started) {
            return;
        }

        $this->reset();

        $this->started = true;
        $this->start_time = microtime(true);
    }

    /**
     * Stops the timer.
     */
    public function stop()
    {
        if (!$this->started) {
            return;
        }

        $this->started = false;
        $this->stop_time = microtime(true);
    }
}