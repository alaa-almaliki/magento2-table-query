<?php declare(strict_types=1);

namespace Alaa\TableQuery\Model;

/**
 * Interface QueryInterface
 *
 * @package Alaa\TableQuery\Model
 * @author  Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
interface QueryInterface
{
    /**
     * @param array $rows
     *                   Example: $rows  = [['name' => 'some name', ...], ['name' => 'other name', ...]]
     * @param array $cols
     * @return void
     */
    public function put(array $rows, array $cols = []);

    /**
     * @param string $where
     * @return void
     */
    public function delete(string $where);

    /**
     * @param string $field
     * @param string $where
     * @return mixed
     */
    public function fetchOne(string $field, string $where);

    /**
     * @param string $where
     * @param array $fields
     * @return array
     */
    public function fetchRow(string $where, array $fields = []): array;

    /**
     * Retrieve the row based on condition and delete it
     *
     * @param string $where
     * @param array $fields
     * @return array
     */
    public function pull(string $where, array $fields = []): array;

    /**
     * @param string|null $where
     * @param array $fields
     * @param int $page
     * @param int $rowCount
     * @param array $columns
     * @return array
     */
    public function fetchAssoc(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array;

    /**
     * @param string|null $where
     * @param array $fields
     * @param int $page
     * @param int $rowCount
     * @param array $columns
     * @return array
     */
    public function fetchAll(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array;

    /**
     * @param string|null $where
     * @param array $fields
     * @param int $page
     * @param int $rowCount
     * @param array $columns
     * @return array
     */
    public function fetchCol(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array;

    /**
     * @param bool $disableForeignKeyCheck
     * @return void
     */
    public function purge(bool $disableForeignKeyCheck = false);

    /**
     * Retrieve first row and delete it
     *
     * @return array
     * @throws \Alaa\TableQuery\Exception\PrimaryIdNotFoundException
     */
    public function poll();

    /**
     * Retrieve last row and delete
     *
     * @return array
     * @throws \Alaa\TableQuery\Exception\PrimaryIdNotFoundException
     */
    public function pop();

    /**
     * @param array $bind
     * @param string $where
     * @return void
     */
    public function update(array $bind, string $where = '');

    /**
     * @return \Alaa\TableQuery\Model\Iterator
     */
    public function iterator(): IteratorInterface;
}
