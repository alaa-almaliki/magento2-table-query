<?php declare(strict_types=1);

namespace Alaa\TableQuery\Model;

/**
 * Interface IteratorInterface
 *
 * @package Alaa\TableQuery\Model
 * @author  Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
interface IteratorInterface extends \Iterator
{
    /**
     * number of rows for each page
     */
    const ROW_COUNT = 20;

    /**
     * @return void
     */
    public function refill();

    /**
     * @return void
     */
    public function reset();
}