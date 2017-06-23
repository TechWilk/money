<?php

namespace Base;

use Account as ChildAccount;
use AccountQuery as ChildAccountQuery;
use Breakdown as ChildBreakdown;
use BreakdownQuery as ChildBreakdownQuery;
use DateTime;
use Exception;
use Hashtag as ChildHashtag;
use HashtagQuery as ChildHashtagQuery;
use Map\BreakdownTableMap;
use Map\TransactionHashtagTableMap;
use Map\TransactionTableMap;
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
use Propel\Runtime\Util\PropelDateTime;
use Transaction as ChildTransaction;
use TransactionHashtag as ChildTransactionHashtag;
use TransactionHashtagQuery as ChildTransactionHashtagQuery;
use TransactionQuery as ChildTransactionQuery;

/**
 * Base class that represents a row from the 'transaction' table.
 */
abstract class Transaction implements ActiveRecordInterface
{
    /**
     * TableMap class name.
     */
    const TABLE_MAP = '\\Map\\TransactionTableMap';

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
     * The value for the date field.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * The value for the value field.
     *
     * @var float
     */
    protected $value;

    /**
     * The value for the description field.
     *
     * @var string
     */
    protected $description;

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
     * @var ObjectCollection|ChildTransactionHashtag[] Collection to store aggregation of ChildTransactionHashtag objects.
     */
    protected $collTransactionHashtags;
    protected $collTransactionHashtagsPartial;

    /**
     * @var ObjectCollection|ChildHashtag[] Cross Collection to store aggregation of ChildHashtag objects.
     */
    protected $collHashtags;

    /**
     * @var bool
     */
    protected $collHashtagsPartial;

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
     * @var ObjectCollection|ChildHashtag[]
     */
    protected $hashtagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     *
     * @var ObjectCollection|ChildBreakdown[]
     */
    protected $breakdownsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     *
     * @var ObjectCollection|ChildTransactionHashtag[]
     */
    protected $transactionHashtagsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Transaction object.
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
     * Compares this with another <code>Transaction</code> instance.  If
     * <code>obj</code> is an instance of <code>Transaction</code>, delegates to
     * <code>equals(Transaction)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Transaction The current object, for fluid interface
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
     * Get the [optionally formatted] temporal [date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     */
    public function getDate($format = null)
    {
        if ($format === null) {
            return $this->date;
        } else {
            return $this->date instanceof \DateTimeInterface ? $this->date->format($format) : null;
        }
    }

    /**
     * Get the [value] column value.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[TransactionTableMap::COL_ID] = true;
        }

        return $this;
    }

 // setId()

    /**
     * Sets the value of [date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *                 Empty strings are treated as NULL.
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->date !== null || $dt !== null) {
            if ($this->date === null || $dt === null || $dt->format('Y-m-d') !== $this->date->format('Y-m-d')) {
                $this->date = $dt === null ? null : clone $dt;
                $this->modifiedColumns[TransactionTableMap::COL_DATE] = true;
            }
        } // if either are not null

        return $this;
    }

 // setDate()

    /**
     * Set the value of [value] column.
     *
     * @param float $v new value
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setValue($v)
    {
        if ($v !== null) {
            $v = (float) $v;
        }

        if ($this->value !== $v) {
            $this->value = $v;
            $this->modifiedColumns[TransactionTableMap::COL_VALUE] = true;
        }

        return $this;
    }

 // setValue()

    /**
     * Set the value of [description] column.
     *
     * @param string $v new value
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[TransactionTableMap::COL_DESCRIPTION] = true;
        }

        return $this;
    }

 // setDescription()

    /**
     * Set the value of [account_id] column.
     *
     * @param int $v new value
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[TransactionTableMap::COL_ACCOUNT_ID] = true;
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
            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : TransactionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : TransactionTableMap::translateFieldName('Date', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->date = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : TransactionTableMap::translateFieldName('Value', TableMap::TYPE_PHPNAME, $indexType)];
            $this->value = (null !== $col) ? (float) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : TransactionTableMap::translateFieldName('Description', TableMap::TYPE_PHPNAME, $indexType)];
            $this->description = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : TransactionTableMap::translateFieldName('AccountId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->account_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = TransactionTableMap::NUM_HYDRATE_COLUMNS.
        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Transaction'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(TransactionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildTransactionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccount = null;
            $this->collBreakdowns = null;

            $this->collTransactionHashtags = null;

            $this->collHashtags = null;
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
     * @see Transaction::setDeleted()
     * @see Transaction::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException('This object has already been deleted.');
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TransactionTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildTransactionQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(TransactionTableMap::DATABASE_NAME);
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
                TransactionTableMap::addInstanceToPool($this);
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

            if ($this->hashtagsScheduledForDeletion !== null) {
                if (!$this->hashtagsScheduledForDeletion->isEmpty()) {
                    $pks = [];
                    foreach ($this->hashtagsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \TransactionHashtagQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->hashtagsScheduledForDeletion = null;
                }
            }

            if ($this->collHashtags) {
                foreach ($this->collHashtags as $hashtag) {
                    if (!$hashtag->isDeleted() && ($hashtag->isNew() || $hashtag->isModified())) {
                        $hashtag->save($con);
                    }
                }
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

            if ($this->transactionHashtagsScheduledForDeletion !== null) {
                if (!$this->transactionHashtagsScheduledForDeletion->isEmpty()) {
                    \TransactionHashtagQuery::create()
                        ->filterByPrimaryKeys($this->transactionHashtagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->transactionHashtagsScheduledForDeletion = null;
                }
            }

            if ($this->collTransactionHashtags !== null) {
                foreach ($this->collTransactionHashtags as $referrerFK) {
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

        $this->modifiedColumns[TransactionTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TransactionTableMap::COL_ID.')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TransactionTableMap::COL_ID)) {
            $modifiedColumns[':p'.$index++] = 'id';
        }
        if ($this->isColumnModified(TransactionTableMap::COL_DATE)) {
            $modifiedColumns[':p'.$index++] = 'date';
        }
        if ($this->isColumnModified(TransactionTableMap::COL_VALUE)) {
            $modifiedColumns[':p'.$index++] = 'value';
        }
        if ($this->isColumnModified(TransactionTableMap::COL_DESCRIPTION)) {
            $modifiedColumns[':p'.$index++] = 'description';
        }
        if ($this->isColumnModified(TransactionTableMap::COL_ACCOUNT_ID)) {
            $modifiedColumns[':p'.$index++] = 'account_id';
        }

        $sql = sprintf(
            'INSERT INTO transaction (%s) VALUES (%s)',
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
                    case 'date':
                        $stmt->bindValue($identifier, $this->date ? $this->date->format('Y-m-d H:i:s.u') : null, PDO::PARAM_STR);
                        break;
                    case 'value':
                        $stmt->bindValue($identifier, $this->value, PDO::PARAM_STR);
                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
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
        $pos = TransactionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getDate();
                break;
            case 2:
                return $this->getValue();
                break;
            case 3:
                return $this->getDescription();
                break;
            case 4:
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
        if (isset($alreadyDumpedObjects['Transaction'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Transaction'][$this->hashCode()] = true;
        $keys = TransactionTableMap::getFieldNames($keyType);
        $result = [
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDate(),
            $keys[2] => $this->getValue(),
            $keys[3] => $this->getDescription(),
            $keys[4] => $this->getAccountId(),
        ];
        if ($result[$keys[1]] instanceof \DateTimeInterface) {
            $result[$keys[1]] = $result[$keys[1]]->format('c');
        }

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
            if (null !== $this->collTransactionHashtags) {
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'transactionHashtags';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'transaction_hashtags';
                        break;
                    default:
                        $key = 'TransactionHashtags';
                }

                $result[$key] = $this->collTransactionHashtags->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Transaction
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TransactionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int   $pos   position in xml schema
     * @param mixed $value field value
     *
     * @return $this|\Transaction
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setDate($value);
                break;
            case 2:
                $this->setValue($value);
                break;
            case 3:
                $this->setDescription($value);
                break;
            case 4:
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
        $keys = TransactionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setDate($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setValue($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setDescription($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setAccountId($arr[$keys[4]]);
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
     * @return $this|\Transaction The current object, for fluid interface
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
        $criteria = new Criteria(TransactionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(TransactionTableMap::COL_ID)) {
            $criteria->add(TransactionTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(TransactionTableMap::COL_DATE)) {
            $criteria->add(TransactionTableMap::COL_DATE, $this->date);
        }
        if ($this->isColumnModified(TransactionTableMap::COL_VALUE)) {
            $criteria->add(TransactionTableMap::COL_VALUE, $this->value);
        }
        if ($this->isColumnModified(TransactionTableMap::COL_DESCRIPTION)) {
            $criteria->add(TransactionTableMap::COL_DESCRIPTION, $this->description);
        }
        if ($this->isColumnModified(TransactionTableMap::COL_ACCOUNT_ID)) {
            $criteria->add(TransactionTableMap::COL_ACCOUNT_ID, $this->account_id);
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
        $criteria = ChildTransactionQuery::create();
        $criteria->add(TransactionTableMap::COL_ID, $this->id);

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
     * @param object $copyObj  An object of \Transaction (or compatible) type.
     * @param bool   $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param bool   $makeNew  Whether to reset autoincrement PKs and make the object new.
     *
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDate($this->getDate());
        $copyObj->setValue($this->getValue());
        $copyObj->setDescription($this->getDescription());
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

            foreach ($this->getTransactionHashtags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTransactionHashtag($relObj->copy($deepCopy));
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
     * @return \Transaction Clone of current object.
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
     * @return $this|\Transaction The current object (for fluent API support)
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
            $v->addTransaction($this);
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
                $this->aAccount->addTransactions($this);
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
        if ('TransactionHashtag' == $relationName) {
            $this->initTransactionHashtags();

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
     * If this ChildTransaction is new, it will return
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
                    ->filterByTransaction($this)
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
     * @return $this|ChildTransaction The current object (for fluent API support)
     */
    public function setBreakdowns(Collection $breakdowns, ConnectionInterface $con = null)
    {
        /** @var ChildBreakdown[] $breakdownsToDelete */
        $breakdownsToDelete = $this->getBreakdowns(new Criteria(), $con)->diff($breakdowns);

        $this->breakdownsScheduledForDeletion = $breakdownsToDelete;

        foreach ($breakdownsToDelete as $breakdownRemoved) {
            $breakdownRemoved->setTransaction(null);
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
                ->filterByTransaction($this)
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
     * @return $this|\Transaction The current object (for fluent API support)
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
        $breakdown->setTransaction($this);
    }

    /**
     * @param ChildBreakdown $breakdown The ChildBreakdown object to remove.
     *
     * @return $this|ChildTransaction The current object (for fluent API support)
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
            $breakdown->setTransaction(null);
        }

        return $this;
    }

    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related Breakdowns from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria            $criteria     optional Criteria object to narrow the query
     * @param ConnectionInterface $con          optional connection object
     * @param string              $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildBreakdown[] List of ChildBreakdown objects
     */
    public function getBreakdownsJoinCategory(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildBreakdownQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getBreakdowns($query, $con);
    }

    /**
     * Clears out the collTransactionHashtags collection.
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     *
     * @see        addTransactionHashtags()
     */
    public function clearTransactionHashtags()
    {
        $this->collTransactionHashtags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTransactionHashtags collection loaded partially.
     */
    public function resetPartialTransactionHashtags($v = true)
    {
        $this->collTransactionHashtagsPartial = $v;
    }

    /**
     * Initializes the collTransactionHashtags collection.
     *
     * By default this just sets the collTransactionHashtags collection to an empty array (like clearcollTransactionHashtags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param bool $overrideExisting If set to true, the method call initializes
     *                               the collection even if it is not empty
     *
     * @return void
     */
    public function initTransactionHashtags($overrideExisting = true)
    {
        if (null !== $this->collTransactionHashtags && !$overrideExisting) {
            return;
        }

        $collectionClassName = TransactionHashtagTableMap::getTableMap()->getCollectionClassName();

        $this->collTransactionHashtags = new $collectionClassName();
        $this->collTransactionHashtags->setModel('\TransactionHashtag');
    }

    /**
     * Gets an array of ChildTransactionHashtag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTransaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria            $criteria optional Criteria object to narrow the query
     * @param ConnectionInterface $con      optional connection object
     *
     * @throws PropelException
     *
     * @return ObjectCollection|ChildTransactionHashtag[] List of ChildTransactionHashtag objects
     */
    public function getTransactionHashtags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTransactionHashtagsPartial && !$this->isNew();
        if (null === $this->collTransactionHashtags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTransactionHashtags) {
                // return empty collection
                $this->initTransactionHashtags();
            } else {
                $collTransactionHashtags = ChildTransactionHashtagQuery::create(null, $criteria)
                    ->filterByTransaction($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTransactionHashtagsPartial && count($collTransactionHashtags)) {
                        $this->initTransactionHashtags(false);

                        foreach ($collTransactionHashtags as $obj) {
                            if (false == $this->collTransactionHashtags->contains($obj)) {
                                $this->collTransactionHashtags->append($obj);
                            }
                        }

                        $this->collTransactionHashtagsPartial = true;
                    }

                    return $collTransactionHashtags;
                }

                if ($partial && $this->collTransactionHashtags) {
                    foreach ($this->collTransactionHashtags as $obj) {
                        if ($obj->isNew()) {
                            $collTransactionHashtags[] = $obj;
                        }
                    }
                }

                $this->collTransactionHashtags = $collTransactionHashtags;
                $this->collTransactionHashtagsPartial = false;
            }
        }

        return $this->collTransactionHashtags;
    }

    /**
     * Sets a collection of ChildTransactionHashtag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection          $transactionHashtags A Propel collection.
     * @param ConnectionInterface $con                 Optional connection object
     *
     * @return $this|ChildTransaction The current object (for fluent API support)
     */
    public function setTransactionHashtags(Collection $transactionHashtags, ConnectionInterface $con = null)
    {
        /** @var ChildTransactionHashtag[] $transactionHashtagsToDelete */
        $transactionHashtagsToDelete = $this->getTransactionHashtags(new Criteria(), $con)->diff($transactionHashtags);

        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->transactionHashtagsScheduledForDeletion = clone $transactionHashtagsToDelete;

        foreach ($transactionHashtagsToDelete as $transactionHashtagRemoved) {
            $transactionHashtagRemoved->setTransaction(null);
        }

        $this->collTransactionHashtags = null;
        foreach ($transactionHashtags as $transactionHashtag) {
            $this->addTransactionHashtag($transactionHashtag);
        }

        $this->collTransactionHashtags = $transactionHashtags;
        $this->collTransactionHashtagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TransactionHashtag objects.
     *
     * @param Criteria            $criteria
     * @param bool                $distinct
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return int Count of related TransactionHashtag objects.
     */
    public function countTransactionHashtags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTransactionHashtagsPartial && !$this->isNew();
        if (null === $this->collTransactionHashtags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTransactionHashtags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTransactionHashtags());
            }

            $query = ChildTransactionHashtagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTransaction($this)
                ->count($con);
        }

        return count($this->collTransactionHashtags);
    }

    /**
     * Method called to associate a ChildTransactionHashtag object to this object
     * through the ChildTransactionHashtag foreign key attribute.
     *
     * @param ChildTransactionHashtag $l ChildTransactionHashtag
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function addTransactionHashtag(ChildTransactionHashtag $l)
    {
        if ($this->collTransactionHashtags === null) {
            $this->initTransactionHashtags();
            $this->collTransactionHashtagsPartial = true;
        }

        if (!$this->collTransactionHashtags->contains($l)) {
            $this->doAddTransactionHashtag($l);

            if ($this->transactionHashtagsScheduledForDeletion and $this->transactionHashtagsScheduledForDeletion->contains($l)) {
                $this->transactionHashtagsScheduledForDeletion->remove($this->transactionHashtagsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildTransactionHashtag $transactionHashtag The ChildTransactionHashtag object to add.
     */
    protected function doAddTransactionHashtag(ChildTransactionHashtag $transactionHashtag)
    {
        $this->collTransactionHashtags[] = $transactionHashtag;
        $transactionHashtag->setTransaction($this);
    }

    /**
     * @param ChildTransactionHashtag $transactionHashtag The ChildTransactionHashtag object to remove.
     *
     * @return $this|ChildTransaction The current object (for fluent API support)
     */
    public function removeTransactionHashtag(ChildTransactionHashtag $transactionHashtag)
    {
        if ($this->getTransactionHashtags()->contains($transactionHashtag)) {
            $pos = $this->collTransactionHashtags->search($transactionHashtag);
            $this->collTransactionHashtags->remove($pos);
            if (null === $this->transactionHashtagsScheduledForDeletion) {
                $this->transactionHashtagsScheduledForDeletion = clone $this->collTransactionHashtags;
                $this->transactionHashtagsScheduledForDeletion->clear();
            }
            $this->transactionHashtagsScheduledForDeletion[] = clone $transactionHashtag;
            $transactionHashtag->setTransaction(null);
        }

        return $this;
    }

    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related TransactionHashtags from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria            $criteria     optional Criteria object to narrow the query
     * @param ConnectionInterface $con          optional connection object
     * @param string              $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildTransactionHashtag[] List of ChildTransactionHashtag objects
     */
    public function getTransactionHashtagsJoinHashtag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTransactionHashtagQuery::create(null, $criteria);
        $query->joinWith('Hashtag', $joinBehavior);

        return $this->getTransactionHashtags($query, $con);
    }

    /**
     * Clears out the collHashtags collection.
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     *
     * @see        addHashtags()
     */
    public function clearHashtags()
    {
        $this->collHashtags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collHashtags crossRef collection.
     *
     * By default this just sets the collHashtags collection to an empty collection (like clearHashtags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initHashtags()
    {
        $collectionClassName = TransactionHashtagTableMap::getTableMap()->getCollectionClassName();

        $this->collHashtags = new $collectionClassName();
        $this->collHashtagsPartial = true;
        $this->collHashtags->setModel('\Hashtag');
    }

    /**
     * Checks if the collHashtags collection is loaded.
     *
     * @return bool
     */
    public function isHashtagsLoaded()
    {
        return null !== $this->collHashtags;
    }

    /**
     * Gets a collection of ChildHashtag objects related by a many-to-many relationship
     * to the current object by way of the transaction_hashtag cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTransaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria            $criteria Optional query object to filter the query
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return ObjectCollection|ChildHashtag[] List of ChildHashtag objects
     */
    public function getHashtags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collHashtagsPartial && !$this->isNew();
        if (null === $this->collHashtags || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collHashtags) {
                    $this->initHashtags();
                }
            } else {
                $query = ChildHashtagQuery::create(null, $criteria)
                    ->filterByTransaction($this);
                $collHashtags = $query->find($con);
                if (null !== $criteria) {
                    return $collHashtags;
                }

                if ($partial && $this->collHashtags) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collHashtags as $obj) {
                        if (!$collHashtags->contains($obj)) {
                            $collHashtags[] = $obj;
                        }
                    }
                }

                $this->collHashtags = $collHashtags;
                $this->collHashtagsPartial = false;
            }
        }

        return $this->collHashtags;
    }

    /**
     * Sets a collection of Hashtag objects related by a many-to-many relationship
     * to the current object by way of the transaction_hashtag cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection          $hashtags A Propel collection.
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return $this|ChildTransaction The current object (for fluent API support)
     */
    public function setHashtags(Collection $hashtags, ConnectionInterface $con = null)
    {
        $this->clearHashtags();
        $currentHashtags = $this->getHashtags();

        $hashtagsScheduledForDeletion = $currentHashtags->diff($hashtags);

        foreach ($hashtagsScheduledForDeletion as $toDelete) {
            $this->removeHashtag($toDelete);
        }

        foreach ($hashtags as $hashtag) {
            if (!$currentHashtags->contains($hashtag)) {
                $this->doAddHashtag($hashtag);
            }
        }

        $this->collHashtagsPartial = false;
        $this->collHashtags = $hashtags;

        return $this;
    }

    /**
     * Gets the number of Hashtag objects related by a many-to-many relationship
     * to the current object by way of the transaction_hashtag cross-reference table.
     *
     * @param Criteria            $criteria Optional query object to filter the query
     * @param bool                $distinct Set to true to force count distinct
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return int the number of related Hashtag objects
     */
    public function countHashtags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collHashtagsPartial && !$this->isNew();
        if (null === $this->collHashtags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collHashtags) {
                return 0;
            } else {
                if ($partial && !$criteria) {
                    return count($this->getHashtags());
                }

                $query = ChildHashtagQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByTransaction($this)
                    ->count($con);
            }
        } else {
            return count($this->collHashtags);
        }
    }

    /**
     * Associate a ChildHashtag to this object
     * through the transaction_hashtag cross reference table.
     *
     * @param ChildHashtag $hashtag
     *
     * @return ChildTransaction The current object (for fluent API support)
     */
    public function addHashtag(ChildHashtag $hashtag)
    {
        if ($this->collHashtags === null) {
            $this->initHashtags();
        }

        if (!$this->getHashtags()->contains($hashtag)) {
            // only add it if the **same** object is not already associated
            $this->collHashtags->push($hashtag);
            $this->doAddHashtag($hashtag);
        }

        return $this;
    }

    /**
     * @param ChildHashtag $hashtag
     */
    protected function doAddHashtag(ChildHashtag $hashtag)
    {
        $transactionHashtag = new ChildTransactionHashtag();

        $transactionHashtag->setHashtag($hashtag);

        $transactionHashtag->setTransaction($this);

        $this->addTransactionHashtag($transactionHashtag);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$hashtag->isTransactionsLoaded()) {
            $hashtag->initTransactions();
            $hashtag->getTransactions()->push($this);
        } elseif (!$hashtag->getTransactions()->contains($this)) {
            $hashtag->getTransactions()->push($this);
        }
    }

    /**
     * Remove hashtag of this object
     * through the transaction_hashtag cross reference table.
     *
     * @param ChildHashtag $hashtag
     *
     * @return ChildTransaction The current object (for fluent API support)
     */
    public function removeHashtag(ChildHashtag $hashtag)
    {
        if ($this->getHashtags()->contains($hashtag)) {
            $transactionHashtag = new ChildTransactionHashtag();
            $transactionHashtag->setHashtag($hashtag);
            if ($hashtag->isTransactionsLoaded()) {
                //remove the back reference if available
                $hashtag->getTransactions()->removeObject($this);
            }

            $transactionHashtag->setTransaction($this);
            $this->removeTransactionHashtag(clone $transactionHashtag);
            $transactionHashtag->clear();

            $this->collHashtags->remove($this->collHashtags->search($hashtag));

            if (null === $this->hashtagsScheduledForDeletion) {
                $this->hashtagsScheduledForDeletion = clone $this->collHashtags;
                $this->hashtagsScheduledForDeletion->clear();
            }

            $this->hashtagsScheduledForDeletion->push($hashtag);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aAccount) {
            $this->aAccount->removeTransaction($this);
        }
        $this->id = null;
        $this->date = null;
        $this->value = null;
        $this->description = null;
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
            if ($this->collTransactionHashtags) {
                foreach ($this->collTransactionHashtags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collHashtags) {
                foreach ($this->collHashtags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collBreakdowns = null;
        $this->collTransactionHashtags = null;
        $this->collHashtags = null;
        $this->aAccount = null;
    }

    /**
     * Return the string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TransactionTableMap::DEFAULT_STRING_FORMAT);
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
