<?php

namespace Base;

use Account as ChildAccount;
use AccountQuery as ChildAccountQuery;
use Breakdown as ChildBreakdown;
use BreakdownQuery as ChildBreakdownQuery;
use Category as ChildCategory;
use CategoryQuery as ChildCategoryQuery;
use Exception;
use Map\BreakdownTableMap;
use Map\CategoryTableMap;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Propel;

/**
 * Base class that represents a row from the 'category' table.
 */
abstract class Category implements ActiveRecordInterface
{
    /**
     * TableMap class name.
     */
    const TABLE_MAP = '\\Map\\CategoryTableMap';

    /**
     * attribute to determine if this object has previously been saved.
     *
     * @var bool
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     *
     * @var array
     */
    protected $modifiedColumns = [];

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset.
     *
     * @var array
     */
    protected $virtualColumns = [];

    /**
     * The value for the id field.
     *
     * @var int
     */
    protected $id;

    /**
     * The value for the name field.
     *
     * @var string
     */
    protected $name;

    /**
     * The value for the account_id field.
     *
     * @var int
     */
    protected $account_id;

    /**
     * @var ChildAccount
     */
    protected $aAccount;

    /**
     * @var ObjectCollection|ChildBreakdown[] Collection to store aggregation of ChildBreakdown objects.
     */
    protected $collBreakdowns;
    protected $collBreakdownsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var bool
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     *
     * @var ObjectCollection|ChildBreakdown[]
     */
    protected $breakdownsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Category object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return bool True if the object has been modified.
     */
    public function isModified()
    {
        return (bool) $this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param string $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     *
     * @return bool True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     *
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return bool true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param bool $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (bool) $b;
    }

    /**
     * Whether this object has been deleted.
     *
     * @return bool The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     *
     * @param bool $b The deleted state of this object.
     *
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (bool) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     *
     * @param string $col If supplied, only the specified column is reset.
     *
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = [];
        }
    }

    /**
     * Compares this with another <code>Category</code> instance.  If
     * <code>obj</code> is an instance of <code>Category</code>, delegates to
     * <code>equals(Category)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param mixed $obj The object to compare to.
     *
     * @return bool Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object.
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object.
     *
     * @param string $name The virtual column name
     *
     * @return bool
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object.
     *
     * @param string $name The virtual column name
     *
     * @throws PropelException
     *
     * @return mixed
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object.
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Category The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param string $msg
     * @param int    $priority One of the Propel::LOG_* logging levels
     *
     * @return bool
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this).': '.$msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>.
     *
     * @param mixed $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param bool  $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     *
     * @return string The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, [], true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing.
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach ($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [account_id] column value.
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     *
     * @return $this|\Category The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CategoryTableMap::COL_ID] = true;
        }

        return $this;
    }

 // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     *
     * @return $this|\Category The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[CategoryTableMap::COL_NAME] = true;
        }

        return $this;
    }

 // setName()

    /**
     * Set the value of [account_id] column.
     *
     * @param int $v new value
     *
     * @return $this|\Category The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[CategoryTableMap::COL_ACCOUNT_ID] = true;
        }

        if ($this->aAccount !== null && $this->aAccount->getId() !== $v) {
            $this->aAccount = null;
        }

        return $this;
    }

 // setAccountId()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return bool Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    }

 // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array  $row       The row returned by DataFetcher->fetch().
     * @param int    $startcol  0-based offset column which indicates which restultset column to start with.
     * @param bool   $rehydrate Whether this object is being re-hydrated from the database.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
     One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     *
     * @return int next starting column
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {
            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CategoryTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CategoryTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CategoryTableMap::translateFieldName('AccountId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->account_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = CategoryTableMap::NUM_HYDRATE_COLUMNS.
        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Category'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aAccount !== null && $this->account_id !== $this->aAccount->getId()) {
            $this->aAccount = null;
        }
    }

 // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param bool                $deep (optional) Whether to also de-associated any related objects.
     * @param ConnectionInterface $con  (optional) The ConnectionInterface connection to use.
     *
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     *
     * @return void
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException('Cannot reload a deleted object.');
        }

        if ($this->isNew()) {
            throw new PropelException('Cannot reload an unsaved object.');
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CategoryTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCategoryQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccount = null;
            $this->collBreakdowns = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return void
     *
     * @see Category::setDeleted()
     * @see Category::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException('This object has already been deleted.');
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildCategoryQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return int The number of rows affected by this insert/update and any referring fk objects' save() operations.
     *
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException('You cannot save an object that has been deleted.');
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CategoryTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return int The number of rows affected by this insert/update and any referring fk objects' save() operations.
     *
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAccount !== null) {
                if ($this->aAccount->isModified() || $this->aAccount->isNew()) {
                    $affectedRows += $this->aAccount->save($con);
                }
                $this->setAccount($this->aAccount);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->breakdownsScheduledForDeletion !== null) {
                if (!$this->breakdownsScheduledForDeletion->isEmpty()) {
                    \BreakdownQuery::create()
                        ->filterByPrimaryKeys($this->breakdownsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->breakdownsScheduledForDeletion = null;
                }
            }

            if ($this->collBreakdowns !== null) {
                foreach ($this->collBreakdowns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;
        }

        return $affectedRows;
    }

 // doSave()

    /**
     * Insert the row in the database.
     *
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = [];
        $index = 0;

        $this->modifiedColumns[CategoryTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CategoryTableMap::COL_ID.')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CategoryTableMap::COL_ID)) {
            $modifiedColumns[':p'.$index++] = 'id';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_NAME)) {
            $modifiedColumns[':p'.$index++] = 'name';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_ACCOUNT_ID)) {
            $modifiedColumns[':p'.$index++] = 'account_id';
        }

        $sql = sprintf(
            'INSERT INTO category (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'account_id':
                        $stmt->bindValue($identifier, $this->account_id, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param ConnectionInterface $con
     *
     * @return int Number of updated rows
     *
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     *
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CategoryTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     *
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getAccountId();
                break;
            default:
                return;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param string $keyType                (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                                       TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                                       Defaults to TableMap::TYPE_PHPNAME.
     * @param bool   $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param array  $alreadyDumpedObjects   List of objects to skip to avoid recursion
     * @param bool   $includeForeignObjects  (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = [], $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Category'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Category'][$this->hashCode()] = true;
        $keys = CategoryTableMap::getFieldNames($keyType);
        $result = [
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getAccountId(),
        ];
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAccount) {
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'account';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'account';
                        break;
                    default:
                        $key = 'Account';
                }

                $result[$key] = $this->aAccount->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->collBreakdowns) {
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'breakdowns';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'breakdowns';
                        break;
                    default:
                        $key = 'Breakdowns';
                }

                $result[$key] = $this->collBreakdowns->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name
     * @param mixed  $value field value
     * @param string $type  The type of fieldname the $name is of:
     *                      one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                      TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                      Defaults to TableMap::TYPE_PHPNAME.
     *
     * @return $this|\Category
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CategoryTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int   $pos   position in xml schema
     * @param mixed $value field value
     *
     * @return $this|\Category
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setAccountId($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     *
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = CategoryTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setAccountId($arr[$keys[2]]);
        }
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed  $parser  A AbstractParser instance,
     *                        or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data    The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Category The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CategoryTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CategoryTableMap::COL_ID)) {
            $criteria->add(CategoryTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(CategoryTableMap::COL_NAME)) {
            $criteria->add(CategoryTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(CategoryTableMap::COL_ACCOUNT_ID)) {
            $criteria->add(CategoryTableMap::COL_ACCOUNT_ID, $this->account_id);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildCategoryQuery::create();
        $criteria->add(CategoryTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     *
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param int $key Primary key.
     *
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     *
     * @return bool
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj  An object of \Category (or compatible) type.
     * @param bool   $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param bool   $makeNew  Whether to reset autoincrement PKs and make the object new.
     *
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setAccountId($this->getAccountId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getBreakdowns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBreakdown($relObj->copy($deepCopy));
                }
            }
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(null); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param bool $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     *
     * @throws PropelException
     *
     * @return \Category Clone of current object.
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildAccount object.
     *
     * @param ChildAccount $v
     *
     * @throws PropelException
     *
     * @return $this|\Category The current object (for fluent API support)
     */
    public function setAccount(ChildAccount $v = null)
    {
        if ($v === null) {
            $this->setAccountId(null);
        } else {
            $this->setAccountId($v->getId());
        }

        $this->aAccount = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildAccount object, it will not be re-added.
        if ($v !== null) {
            $v->addCategory($this);
        }

        return $this;
    }

    /**
     * Get the associated ChildAccount object.
     *
     * @param ConnectionInterface $con Optional Connection object.
     *
     * @throws PropelException
     *
     * @return ChildAccount The associated ChildAccount object.
     */
    public function getAccount(ConnectionInterface $con = null)
    {
        if ($this->aAccount === null && ($this->account_id !== null)) {
            $this->aAccount = ChildAccountQuery::create()->findPk($this->account_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccount->addCategories($this);
             */
        }

        return $this->aAccount;
    }

    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     *
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Breakdown' == $relationName) {
            $this->initBreakdowns();

            return;
        }
    }

    /**
     * Clears out the collBreakdowns collection.
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     *
     * @see        addBreakdowns()
     */
    public function clearBreakdowns()
    {
        $this->collBreakdowns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collBreakdowns collection loaded partially.
     */
    public function resetPartialBreakdowns($v = true)
    {
        $this->collBreakdownsPartial = $v;
    }

    /**
     * Initializes the collBreakdowns collection.
     *
     * By default this just sets the collBreakdowns collection to an empty array (like clearcollBreakdowns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param bool $overrideExisting If set to true, the method call initializes
     *                               the collection even if it is not empty
     *
     * @return void
     */
    public function initBreakdowns($overrideExisting = true)
    {
        if (null !== $this->collBreakdowns && !$overrideExisting) {
            return;
        }

        $collectionClassName = BreakdownTableMap::getTableMap()->getCollectionClassName();

        $this->collBreakdowns = new $collectionClassName();
        $this->collBreakdowns->setModel('\Breakdown');
    }

    /**
     * Gets an array of ChildBreakdown objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria            $criteria optional Criteria object to narrow the query
     * @param ConnectionInterface $con      optional connection object
     *
     * @throws PropelException
     *
     * @return ObjectCollection|ChildBreakdown[] List of ChildBreakdown objects
     */
    public function getBreakdowns(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collBreakdownsPartial && !$this->isNew();
        if (null === $this->collBreakdowns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBreakdowns) {
                // return empty collection
                $this->initBreakdowns();
            } else {
                $collBreakdowns = ChildBreakdownQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collBreakdownsPartial && count($collBreakdowns)) {
                        $this->initBreakdowns(false);

                        foreach ($collBreakdowns as $obj) {
                            if (false == $this->collBreakdowns->contains($obj)) {
                                $this->collBreakdowns->append($obj);
                            }
                        }

                        $this->collBreakdownsPartial = true;
                    }

                    return $collBreakdowns;
                }

                if ($partial && $this->collBreakdowns) {
                    foreach ($this->collBreakdowns as $obj) {
                        if ($obj->isNew()) {
                            $collBreakdowns[] = $obj;
                        }
                    }
                }

                $this->collBreakdowns = $collBreakdowns;
                $this->collBreakdownsPartial = false;
            }
        }

        return $this->collBreakdowns;
    }

    /**
     * Sets a collection of ChildBreakdown objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection          $breakdowns A Propel collection.
     * @param ConnectionInterface $con        Optional connection object
     *
     * @return $this|ChildCategory The current object (for fluent API support)
     */
    public function setBreakdowns(Collection $breakdowns, ConnectionInterface $con = null)
    {
        /** @var ChildBreakdown[] $breakdownsToDelete */
        $breakdownsToDelete = $this->getBreakdowns(new Criteria(), $con)->diff($breakdowns);

        $this->breakdownsScheduledForDeletion = $breakdownsToDelete;

        foreach ($breakdownsToDelete as $breakdownRemoved) {
            $breakdownRemoved->setCategory(null);
        }

        $this->collBreakdowns = null;
        foreach ($breakdowns as $breakdown) {
            $this->addBreakdown($breakdown);
        }

        $this->collBreakdowns = $breakdowns;
        $this->collBreakdownsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Breakdown objects.
     *
     * @param Criteria            $criteria
     * @param bool                $distinct
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return int Count of related Breakdown objects.
     */
    public function countBreakdowns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collBreakdownsPartial && !$this->isNew();
        if (null === $this->collBreakdowns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBreakdowns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBreakdowns());
            }

            $query = ChildBreakdownQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collBreakdowns);
    }

    /**
     * Method called to associate a ChildBreakdown object to this object
     * through the ChildBreakdown foreign key attribute.
     *
     * @param ChildBreakdown $l ChildBreakdown
     *
     * @return $this|\Category The current object (for fluent API support)
     */
    public function addBreakdown(ChildBreakdown $l)
    {
        if ($this->collBreakdowns === null) {
            $this->initBreakdowns();
            $this->collBreakdownsPartial = true;
        }

        if (!$this->collBreakdowns->contains($l)) {
            $this->doAddBreakdown($l);

            if ($this->breakdownsScheduledForDeletion and $this->breakdownsScheduledForDeletion->contains($l)) {
                $this->breakdownsScheduledForDeletion->remove($this->breakdownsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildBreakdown $breakdown The ChildBreakdown object to add.
     */
    protected function doAddBreakdown(ChildBreakdown $breakdown)
    {
        $this->collBreakdowns[] = $breakdown;
        $breakdown->setCategory($this);
    }

    /**
     * @param ChildBreakdown $breakdown The ChildBreakdown object to remove.
     *
     * @return $this|ChildCategory The current object (for fluent API support)
     */
    public function removeBreakdown(ChildBreakdown $breakdown)
    {
        if ($this->getBreakdowns()->contains($breakdown)) {
            $pos = $this->collBreakdowns->search($breakdown);
            $this->collBreakdowns->remove($pos);
            if (null === $this->breakdownsScheduledForDeletion) {
                $this->breakdownsScheduledForDeletion = clone $this->collBreakdowns;
                $this->breakdownsScheduledForDeletion->clear();
            }
            $this->breakdownsScheduledForDeletion[] = clone $breakdown;
            $breakdown->setCategory(null);
        }

        return $this;
    }

    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related Breakdowns from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param Criteria            $criteria     optional Criteria object to narrow the query
     * @param ConnectionInterface $con          optional connection object
     * @param string              $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildBreakdown[] List of ChildBreakdown objects
     */
    public function getBreakdownsJoinTransaction(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildBreakdownQuery::create(null, $criteria);
        $query->joinWith('Transaction', $joinBehavior);

        return $this->getBreakdowns($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aAccount) {
            $this->aAccount->removeCategory($this);
        }
        $this->id = null;
        $this->name = null;
        $this->account_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param bool $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collBreakdowns) {
                foreach ($this->collBreakdowns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collBreakdowns = null;
        $this->aAccount = null;
    }

    /**
     * Return the string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CategoryTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object.
     *
     * @param ConnectionInterface $con
     *
     * @return bool
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }

        return true;
    }

    /**
     * Code to be run after persisting the object.
     *
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database.
     *
     * @param ConnectionInterface $con
     *
     * @return bool
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }

        return true;
    }

    /**
     * Code to be run after inserting to database.
     *
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database.
     *
     * @param ConnectionInterface $con
     *
     * @return bool
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }

        return true;
    }

    /**
     * Code to be run after updating the object in database.
     *
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database.
     *
     * @param ConnectionInterface $con
     *
     * @return bool
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }

        return true;
    }

    /**
     * Code to be run after deleting the object in database.
     *
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }

    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }
}
