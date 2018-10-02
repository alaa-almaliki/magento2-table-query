<?php declare(strict_types=1);

namespace Alaa\TableQuery\Model;

use Magento\Framework\App\ResourceConnection;
use Zend\Db\Sql\Select;

/**
 * Class Query
 *
 * @package Alaa\TableQuery\Model
 * @author  Alaa Al-Maliki <alaa.almaliki@gmail.com>
 */
class Query implements QueryInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var null|string
     */
    protected $primaryId;

    /**
     * @var IteratorInterfaceFactory
     */
    protected $iteratorFactory;

    /**
     * Query constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param IteratorInterfaceFactory $iteratorFactory
     * @param string $table
     * @param string $primaryId
     * @throws \Alaa\TableQuery\Exception\TableNotExistsException
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        IteratorInterfaceFactory $iteratorFactory,
        string $table,
        string $primaryId
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->iteratorFactory = $iteratorFactory;
        $this->table = $table;

        if (!$this->connection->isTableExists($this->table)) {
            throw new \Alaa\TableQuery\Exception\TableNotExistsException(
                \sprintf('%s is not exists', $this->table)
            );
        }

        $this->primaryId = $primaryId;
    }

    /**
     * @inheritdoc
     */
    public function put(array $rows, array $cols = [])
    {
        if (!\is_array(\current($rows))) {
            $rows = [$rows];
        }

        if (empty($cols)) {
            $cols = \array_keys(\current($rows));
        }

        $this->connection->insertArray($this->table, $cols, $rows);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $where)
    {
        $this->connection->delete($this->table, $where);
    }

    /**
     * @inheritdoc
     */
    public function fetchOne(string $field, string $where)
    {
        $select = $this->getSelect()
            ->from($this->table, [$field])
            ->where($where);

        return $this->connection->fetchOne($select);
    }

    /**
     * @inheritdoc
     */
    public function fetchAssoc(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array {
        $select = $this->getSelect();
        $this->prepareSelectToFetch($select, $where, $fields, $page, $rowCount, $columns);
        return $this->connection->fetchAssoc($select);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array {
        $select = $this->getSelect();
        $this->prepareSelectToFetch($select, $where, $fields, $page, $rowCount, $columns);
        return $this->connection->fetchAll($select);
    }

    /**
     * @inheritdoc
     */
    public function fetchCol(
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ): array {
        $select = $this->getSelect();
        $this->prepareSelectToFetch($select, $where, $fields, $page, $rowCount, $columns);
        return $this->connection->fetchCol($select);
    }

    /**
     * @inheritdoc
     */
    public function fetchRow(string $where, array $fields = []): array
    {
        $select = $this->getSelect()
            ->from($this->table, $this->resolveFields($fields))
            ->where($where);

        return $this->connection->fetchRow($select);
    }

    /**
     * @inheritdoc
     */
    public function pull(string $where, array $fields = []): array
    {
        $row = $this->fetchRow($where, $fields);
        if (!\array_key_exists($this->primaryId, $row)) {
            throw new \Alaa\TableQuery\Exception\PrimaryIdNotFoundException(
                \sprintf('Primary Id %s is not found in result', $this->primaryId)
            );
        }
        $this->delete(\sprintf('%s = %d', $this->primaryId, (int) $row[$this->primaryId]));
        return $row;
    }

    /**
     * @inheritdoc
     */
    public function poll()
    {
        $row = $this->getRow();
        if (!\array_key_exists($this->primaryId, $row)) {
            throw new \Alaa\TableQuery\Exception\PrimaryIdNotFoundException(
                \sprintf('Primary Id %s is not found in result', $this->primaryId)
            );
        }
        $this->delete(\sprintf('%s = %d', $this->primaryId, (int) $row[$this->primaryId]));
        return $row;

    }

    /**
     * @inheritdoc
     */
    public function pop()
    {
        $row = $this->getRow(Select::ORDER_DESCENDING);
        if (!\array_key_exists($this->primaryId, $row)) {
            throw new \Alaa\TableQuery\Exception\PrimaryIdNotFoundException(
                \sprintf('Primary Id %s is not found in result', $this->primaryId)
            );
        }
        $this->delete(\sprintf('%s = %d', $this->primaryId, (int) $row[$this->primaryId]));
        return $row;
    }

    /**
     * @inheritdoc
     */
    public function purge(bool $disableForeignKeyCheck = false)
    {
        if ($disableForeignKeyCheck) {
            $this->connection->startSetup();
            $this->connection->truncateTable($this->table);
            $this->connection->endSetup();
        } else {
            $this->connection->truncateTable($this->table);
        }
    }

    /**
     * @inheritdoc
     */
    public function update(array $bind, string $where = '')
    {
        $this->connection->update($this->table, $bind, $where);
    }

    /**
     * @inheritdoc
     */
    public function iterator(): IteratorInterface
    {
        return $this->iteratorFactory->create(['query' => $this]);
    }

    /**
     * @param string $order
     * @return array
     */
    private function getRow(string $order = Select::ORDER_ASCENDING): array
    {
        $select = $this->getSelect()
            ->from($this->table)
            ->order(\sprintf('%s.%s %s', $this->table, $this->primaryId, $order))
            ->limit(1);

        return $this->connection->fetchRow($select);
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    private function getSelect(): \Magento\Framework\DB\Select
    {
        return $this->connection->select();
    }

    /**
     * @param array $fields
     * @return array|string
     */
    private function resolveFields(array $fields)
    {
        $fieldsToSelect = '*';
        if (!empty($fields)) {
            $fieldsToSelect = $fields;
        }

        return $fieldsToSelect;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param int $page
     * @param int $rowCount
     * @return void
     */
    private function paginate(\Magento\Framework\DB\Select $select, int $page, int $rowCount)
    {
        if ($page > 0 && $rowCount > 0) {
            $select->limitPage($page, $rowCount);
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string|null $where
     * @param array $fields
     * @param int $page
     * @param int $rowCount
     * @param array $columns
     */
    private function prepareSelectToFetch(
        \Magento\Framework\DB\Select $select,
        string $where = null,
        array $fields = [],
        int $page = 0,
        int $rowCount = 0,
        array $columns = []
    ) {
        $select->from($this->table, $this->resolveFields($fields));

        if (null !== $where) {
            $select->where($where);
        }

        if (!empty($columns)) {
            $select->columns($columns);
        }
        $this->paginate($select, $page, $rowCount);
    }
}