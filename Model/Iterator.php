<?php declare(strict_types=1);

namespace Alaa\TableQuery\Model;

/**
 * Class RowIterator
 *
 * @package Alaa\TableQuery\Model
 * @author  Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
class Iterator implements IteratorInterface
{
    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var bool
     */
    protected $hasMoreResults = true;

    /**
     * RowIterator constructor.
     *
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
        $this->refill();
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->data[$this->position];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $isValid =  isset($this->data[$this->position]);
        if (!$isValid && $this->hasMoreResults) {
            $this->refill();
            $isValid =  isset($this->data[$this->position]);
        }

        return $isValid;

    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->reset();
        $this->page = 1;
        $this->refill();
    }

    /**
     * @inheritdoc
     */
    public function refill()
    {
        $result = $this->query->fetchAll(null, [], $this->page, self::ROW_COUNT);
        if (\is_array($result) && \count($result) > 0) {
            $this->reset();
            foreach ($result as $row) {
                $this->data[] = $row;
            }
            ++$this->page;
        } else {
            $this->hasMoreResults = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {
        $this->data = [];
        $this->position = 0;
    }
}