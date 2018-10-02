# Magento 2 Table Query

A simple module works as a wrapper around Magento connection to perform CRUD operations on a db table

# Installation
`composer require alaa/magento2-table-query`


# How it works

### Using Factory
```
public function __construct(\Alaa\TableQuery\Model\QueryInterfaceFactory $queryFactory)
{
        $this->queryFactory = $queryFactory;
}
```


### Create query object
```
$query =  $this->queryFactory->create(['table' => 'my_table', 'primaryId' => 'primary_id']);
```

### Add new records

```
$data = [
    [
        'name' => 'some name'
        'age' => 33,
        ...
    ],
    [...]
]
$query->put($data);
```

### To Retrieve records use methods start with fetch

### To retrieve records and delete them:

Retrieve a record based on condition
```
$query->pull('id = 5')
```

Retrieve first record and delete it
```
$query->poll()
```

Retrieve last record and delete it
```
$query->pop()
```

### Truncate a table
```
$query->purge();
```

### Update a record
```
$query->update(...)
```

### Using the Iterator
```
$it = $query->iterator();

while ($it->valid()) {
    $row = $it->current();
    // ... do something with row
    $it->next();
}
```

# Contribution
Feel free to raise issues and contribute

# License
MIT