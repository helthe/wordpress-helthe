<?php

/*
 * This file is part of the WordPress Helthe plugin.
 *
 * (c) Helthe
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Fatal Error Exception distinguishes fatal errors from regular error exceptions.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Exception_FatalErrorException extends \ErrorException
{
}