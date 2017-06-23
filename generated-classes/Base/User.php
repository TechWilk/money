<?php

namespace Base;

use Account as ChildAccount;
use AccountQuery as ChildAccountQuery;
use DateTime;
use Exception;
use Map\UserAccountsTableMap;
use Map\UserTableMap;
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
use User as ChildUser;
use UserAccounts as ChildUserAccounts;
use UserAccountsQuery as ChildUserAccountsQuery;
use UserQuery as ChildUserQuery;

/**
 * Base class that represents a row from the 'user' table.
 */
abstract class User implements ActiveRecordInterface
{
    /**
     * TableMap class name.
     */
    const TABLE_MAP = '\\Map\\UserTableMap';

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
     * The value for the first_name field.
     *
     * @var string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     *
     * @var string
     */
    protected $last_name;

    /**
     * The value for the email field.
     *
     * @var \EmailAddress
     */
    protected $email;

    /**
     * The value for the password_hash field.
     *
     * @var string
     */
    protected $password_hash;

    /**
     * The value for the password_expire field.
     *
     * @var DateTime
     */
    protected $password_expire;

    /**
     * The value for the enable field.
     *
     * Note: this column has a database default value of: true
     *
     * @var bool
     */
    protected $enable;

    /**
     * @var ObjectCollection|ChildUserAccounts[] Collection to store aggregation of ChildUserAccounts objects.
     */
    protected $collUserAccountss;
    protected $collUserAccountssPartial;

    /**
     * @var ObjectCollection|ChildAccount[] Cross Collection to store aggregation of ChildAccount objects.
     */
    protected $collAccounts;

    /**
     * @var bool
     */
    protected $collAccountsPartial;

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
     * @var ObjectCollection|ChildAccount[]
     */
    protected $accountsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     *
     * @var ObjectCollection|ChildUserAccounts[]
     */
    protected $userAccountssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     *
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->enable = true;
    }

    /**
     * Initializes internal state of Base\User object.
     *
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
     * Compares this with another <code>User</code> instance.  If
     * <code>obj</code> is an instance of <code>User</code>, delegates to
     * <code>equals(User)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|User The current object, for fluid interface
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
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Get the [email] column value.
     *
     * @return \EmailAddress
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the [password_hash] column value.
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Get the [optionally formatted] temporal [password_expire] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     */
    public function getPasswordExpire($format = null)
    {
        if ($format === null) {
            return $this->password_expire;
        } else {
            return $this->password_expire instanceof \DateTimeInterface ? $this->password_expire->format($format) : null;
        }
    }

    /**
     * Get the [enable] column value.
     *
     * @return bool
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Get the [enable] column value.
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getEnable();
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[UserTableMap::COL_ID] = true;
        }

        return $this;
    }

 // setId()

    /**
     * Set the value of [first_name] column.
     *
     * @param string $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[UserTableMap::COL_FIRST_NAME] = true;
        }

        return $this;
    }

 // setFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param string $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[UserTableMap::COL_LAST_NAME] = true;
        }

        return $this;
    }

 // setLastName()

    /**
     * Set the value of [email] column.
     *
     * @param \EmailAddress $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setEmail(\EmailAddress $v)
    {
        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[UserTableMap::COL_EMAIL] = true;
        }

        return $this;
    }

 // setEmail()

    /**
     * Set the value of [password_hash] column.
     *
     * @param string $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setPasswordHash($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password_hash !== $v) {
            $this->password_hash = $v;
            $this->modifiedColumns[UserTableMap::COL_PASSWORD_HASH] = true;
        }

        return $this;
    }

 // setPasswordHash()

    /**
     * Sets the value of [password_expire] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *                 Empty strings are treated as NULL.
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setPasswordExpire($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->password_expire !== null || $dt !== null) {
            if ($this->password_expire === null || $dt === null || $dt->format('Y-m-d H:i:s.u') !== $this->password_expire->format('Y-m-d H:i:s.u')) {
                $this->password_expire = $dt === null ? null : clone $dt;
                $this->modifiedColumns[UserTableMap::COL_PASSWORD_EXPIRE] = true;
            }
        } // if either are not null

        return $this;
    }

 // setPasswordExpire()

    /**
     * Sets the value of the [enable] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param bool|int|string $v The new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setEnable($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), ['false', 'off', '-', 'no', 'n', '0', '']) ? false : true;
            } else {
                $v = (bool) $v;
            }
        }

        if ($this->enable !== $v) {
            $this->enable = $v;
            $this->modifiedColumns[UserTableMap::COL_ENABLE] = true;
        }

        return $this;
    }

 // setEnable()

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
        if ($this->enable !== true) {
            return false;
        }

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
            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : UserTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : UserTableMap::translateFieldName('FirstName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->first_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : UserTableMap::translateFieldName('LastName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->last_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : UserTableMap::translateFieldName('Email', TableMap::TYPE_PHPNAME, $indexType)];
            $this->email = (null !== $col) ? new \EmailAddress($col) : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : UserTableMap::translateFieldName('PasswordHash', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password_hash = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : UserTableMap::translateFieldName('PasswordExpire', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->password_expire = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : UserTableMap::translateFieldName('Enable', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable = (null !== $col) ? (bool) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = UserTableMap::NUM_HYDRATE_COLUMNS.
        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\User'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildUserQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collUserAccountss = null;

            $this->collAccounts = null;
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
     * @see User::setDeleted()
     * @see User::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException('This object has already been deleted.');
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildUserQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
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
                UserTableMap::addInstanceToPool($this);
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

            if ($this->accountsScheduledForDeletion !== null) {
                if (!$this->accountsScheduledForDeletion->isEmpty()) {
                    $pks = [];
                    foreach ($this->accountsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \UserAccountsQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->accountsScheduledForDeletion = null;
                }
            }

            if ($this->collAccounts) {
                foreach ($this->collAccounts as $account) {
                    if (!$account->isDeleted() && ($account->isNew() || $account->isModified())) {
                        $account->save($con);
                    }
                }
            }

            if ($this->userAccountssScheduledForDeletion !== null) {
                if (!$this->userAccountssScheduledForDeletion->isEmpty()) {
                    \UserAccountsQuery::create()
                        ->filterByPrimaryKeys($this->userAccountssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userAccountssScheduledForDeletion = null;
                }
            }

            if ($this->collUserAccountss !== null) {
                foreach ($this->collUserAccountss as $referrerFK) {
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

        $this->modifiedColumns[UserTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.UserTableMap::COL_ID.')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserTableMap::COL_ID)) {
            $modifiedColumns[':p'.$index++] = 'id';
        }
        if ($this->isColumnModified(UserTableMap::COL_FIRST_NAME)) {
            $modifiedColumns[':p'.$index++] = 'first_name';
        }
        if ($this->isColumnModified(UserTableMap::COL_LAST_NAME)) {
            $modifiedColumns[':p'.$index++] = 'last_name';
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL)) {
            $modifiedColumns[':p'.$index++] = 'email';
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_HASH)) {
            $modifiedColumns[':p'.$index++] = 'password_hash';
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_EXPIRE)) {
            $modifiedColumns[':p'.$index++] = 'password_expire';
        }
        if ($this->isColumnModified(UserTableMap::COL_ENABLE)) {
            $modifiedColumns[':p'.$index++] = 'enable';
        }

        $sql = sprintf(
            'INSERT INTO user (%s) VALUES (%s)',
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
                    case 'first_name':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case 'last_name':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case 'email':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'password_hash':
                        $stmt->bindValue($identifier, $this->password_hash, PDO::PARAM_STR);
                        break;
                    case 'password_expire':
                        $stmt->bindValue($identifier, $this->password_expire ? $this->password_expire->format('Y-m-d H:i:s.u') : null, PDO::PARAM_STR);
                        break;
                    case 'enable':
                        $stmt->bindValue($identifier, (int) $this->enable, PDO::PARAM_INT);
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
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getFirstName();
                break;
            case 2:
                return $this->getLastName();
                break;
            case 3:
                return $this->getEmail();
                break;
            case 4:
                return $this->getPasswordHash();
                break;
            case 5:
                return $this->getPasswordExpire();
                break;
            case 6:
                return $this->getEnable();
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
        if (isset($alreadyDumpedObjects['User'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->hashCode()] = true;
        $keys = UserTableMap::getFieldNames($keyType);
        $result = [
            $keys[0] => $this->getId(),
            $keys[1] => $this->getFirstName(),
            $keys[2] => $this->getLastName(),
            $keys[3] => $this->getEmail(),
            $keys[4] => $this->getPasswordHash(),
            $keys[5] => $this->getPasswordExpire(),
            $keys[6] => $this->getEnable(),
        ];
        if ($result[$keys[5]] instanceof \DateTimeInterface) {
            $result[$keys[5]] = $result[$keys[5]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collUserAccountss) {
                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'userAccountss';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'user_accountss';
                        break;
                    default:
                        $key = 'UserAccountss';
                }

                $result[$key] = $this->collUserAccountss->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\User
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int   $pos   position in xml schema
     * @param mixed $value field value
     *
     * @return $this|\User
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setFirstName($value);
                break;
            case 2:
                $this->setLastName($value);
                break;
            case 3:
                $this->setEmail($value);
                break;
            case 4:
                $this->setPasswordHash($value);
                break;
            case 5:
                $this->setPasswordExpire($value);
                break;
            case 6:
                $this->setEnable($value);
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
        $keys = UserTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setFirstName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setLastName($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setEmail($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setPasswordHash($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setPasswordExpire($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setEnable($arr[$keys[6]]);
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
     * @return $this|\User The current object, for fluid interface
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
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);

        if ($this->isColumnModified(UserTableMap::COL_ID)) {
            $criteria->add(UserTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(UserTableMap::COL_FIRST_NAME)) {
            $criteria->add(UserTableMap::COL_FIRST_NAME, $this->first_name);
        }
        if ($this->isColumnModified(UserTableMap::COL_LAST_NAME)) {
            $criteria->add(UserTableMap::COL_LAST_NAME, $this->last_name);
        }
        if ($this->isColumnModified(UserTableMap::COL_EMAIL)) {
            $criteria->add(UserTableMap::COL_EMAIL, $this->email);
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_HASH)) {
            $criteria->add(UserTableMap::COL_PASSWORD_HASH, $this->password_hash);
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD_EXPIRE)) {
            $criteria->add(UserTableMap::COL_PASSWORD_EXPIRE, $this->password_expire);
        }
        if ($this->isColumnModified(UserTableMap::COL_ENABLE)) {
            $criteria->add(UserTableMap::COL_ENABLE, $this->enable);
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
        $criteria = ChildUserQuery::create();
        $criteria->add(UserTableMap::COL_ID, $this->id);

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
     * @param object $copyObj  An object of \User (or compatible) type.
     * @param bool   $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param bool   $makeNew  Whether to reset autoincrement PKs and make the object new.
     *
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPasswordHash($this->getPasswordHash());
        $copyObj->setPasswordExpire($this->getPasswordExpire());
        $copyObj->setEnable($this->getEnable());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getUserAccountss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserAccounts($relObj->copy($deepCopy));
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
     * @return \User Clone of current object.
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
        if ('UserAccounts' == $relationName) {
            $this->initUserAccountss();

            return;
        }
    }

    /**
     * Clears out the collUserAccountss collection.
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     *
     * @see        addUserAccountss()
     */
    public function clearUserAccountss()
    {
        $this->collUserAccountss = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserAccountss collection loaded partially.
     */
    public function resetPartialUserAccountss($v = true)
    {
        $this->collUserAccountssPartial = $v;
    }

    /**
     * Initializes the collUserAccountss collection.
     *
     * By default this just sets the collUserAccountss collection to an empty array (like clearcollUserAccountss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param bool $overrideExisting If set to true, the method call initializes
     *                               the collection even if it is not empty
     *
     * @return void
     */
    public function initUserAccountss($overrideExisting = true)
    {
        if (null !== $this->collUserAccountss && !$overrideExisting) {
            return;
        }

        $collectionClassName = UserAccountsTableMap::getTableMap()->getCollectionClassName();

        $this->collUserAccountss = new $collectionClassName();
        $this->collUserAccountss->setModel('\UserAccounts');
    }

    /**
     * Gets an array of ChildUserAccounts objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria            $criteria optional Criteria object to narrow the query
     * @param ConnectionInterface $con      optional connection object
     *
     * @throws PropelException
     *
     * @return ObjectCollection|ChildUserAccounts[] List of ChildUserAccounts objects
     */
    public function getUserAccountss(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserAccountssPartial && !$this->isNew();
        if (null === $this->collUserAccountss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserAccountss) {
                // return empty collection
                $this->initUserAccountss();
            } else {
                $collUserAccountss = ChildUserAccountsQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserAccountssPartial && count($collUserAccountss)) {
                        $this->initUserAccountss(false);

                        foreach ($collUserAccountss as $obj) {
                            if (false == $this->collUserAccountss->contains($obj)) {
                                $this->collUserAccountss->append($obj);
                            }
                        }

                        $this->collUserAccountssPartial = true;
                    }

                    return $collUserAccountss;
                }

                if ($partial && $this->collUserAccountss) {
                    foreach ($this->collUserAccountss as $obj) {
                        if ($obj->isNew()) {
                            $collUserAccountss[] = $obj;
                        }
                    }
                }

                $this->collUserAccountss = $collUserAccountss;
                $this->collUserAccountssPartial = false;
            }
        }

        return $this->collUserAccountss;
    }

    /**
     * Sets a collection of ChildUserAccounts objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection          $userAccountss A Propel collection.
     * @param ConnectionInterface $con           Optional connection object
     *
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setUserAccountss(Collection $userAccountss, ConnectionInterface $con = null)
    {
        /** @var ChildUserAccounts[] $userAccountssToDelete */
        $userAccountssToDelete = $this->getUserAccountss(new Criteria(), $con)->diff($userAccountss);

        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->userAccountssScheduledForDeletion = clone $userAccountssToDelete;

        foreach ($userAccountssToDelete as $userAccountsRemoved) {
            $userAccountsRemoved->setUser(null);
        }

        $this->collUserAccountss = null;
        foreach ($userAccountss as $userAccounts) {
            $this->addUserAccounts($userAccounts);
        }

        $this->collUserAccountss = $userAccountss;
        $this->collUserAccountssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserAccounts objects.
     *
     * @param Criteria            $criteria
     * @param bool                $distinct
     * @param ConnectionInterface $con
     *
     * @throws PropelException
     *
     * @return int Count of related UserAccounts objects.
     */
    public function countUserAccountss(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserAccountssPartial && !$this->isNew();
        if (null === $this->collUserAccountss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserAccountss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserAccountss());
            }

            $query = ChildUserAccountsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserAccountss);
    }

    /**
     * Method called to associate a ChildUserAccounts object to this object
     * through the ChildUserAccounts foreign key attribute.
     *
     * @param ChildUserAccounts $l ChildUserAccounts
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function addUserAccounts(ChildUserAccounts $l)
    {
        if ($this->collUserAccountss === null) {
            $this->initUserAccountss();
            $this->collUserAccountssPartial = true;
        }

        if (!$this->collUserAccountss->contains($l)) {
            $this->doAddUserAccounts($l);

            if ($this->userAccountssScheduledForDeletion and $this->userAccountssScheduledForDeletion->contains($l)) {
                $this->userAccountssScheduledForDeletion->remove($this->userAccountssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildUserAccounts $userAccounts The ChildUserAccounts object to add.
     */
    protected function doAddUserAccounts(ChildUserAccounts $userAccounts)
    {
        $this->collUserAccountss[] = $userAccounts;
        $userAccounts->setUser($this);
    }

    /**
     * @param ChildUserAccounts $userAccounts The ChildUserAccounts object to remove.
     *
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function removeUserAccounts(ChildUserAccounts $userAccounts)
    {
        if ($this->getUserAccountss()->contains($userAccounts)) {
            $pos = $this->collUserAccountss->search($userAccounts);
            $this->collUserAccountss->remove($pos);
            if (null === $this->userAccountssScheduledForDeletion) {
                $this->userAccountssScheduledForDeletion = clone $this->collUserAccountss;
                $this->userAccountssScheduledForDeletion->clear();
            }
            $this->userAccountssScheduledForDeletion[] = clone $userAccounts;
            $userAccounts->setUser(null);
        }

        return $this;
    }

    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserAccountss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria            $criteria     optional Criteria object to narrow the query
     * @param ConnectionInterface $con          optional connection object
     * @param string              $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildUserAccounts[] List of ChildUserAccounts objects
     */
    public function getUserAccountssJoinAccount(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserAccountsQuery::create(null, $criteria);
        $query->joinWith('Account', $joinBehavior);

        return $this->getUserAccountss($query, $con);
    }

    /**
     * Clears out the collAccounts collection.
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     *
     * @see        addAccounts()
     */
    public function clearAccounts()
    {
        $this->collAccounts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collAccounts crossRef collection.
     *
     * By default this just sets the collAccounts collection to an empty collection (like clearAccounts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initAccounts()
    {
        $collectionClassName = UserAccountsTableMap::getTableMap()->getCollectionClassName();

        $this->collAccounts = new $collectionClassName();
        $this->collAccountsPartial = true;
        $this->collAccounts->setModel('\Account');
    }

    /**
     * Checks if the collAccounts collection is loaded.
     *
     * @return bool
     */
    public function isAccountsLoaded()
    {
        return null !== $this->collAccounts;
    }

    /**
     * Gets a collection of ChildAccount objects related by a many-to-many relationship
     * to the current object by way of the user_accounts cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria            $criteria Optional query object to filter the query
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return ObjectCollection|ChildAccount[] List of ChildAccount objects
     */
    public function getAccounts(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAccountsPartial && !$this->isNew();
        if (null === $this->collAccounts || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collAccounts) {
                    $this->initAccounts();
                }
            } else {
                $query = ChildAccountQuery::create(null, $criteria)
                    ->filterByUser($this);
                $collAccounts = $query->find($con);
                if (null !== $criteria) {
                    return $collAccounts;
                }

                if ($partial && $this->collAccounts) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collAccounts as $obj) {
                        if (!$collAccounts->contains($obj)) {
                            $collAccounts[] = $obj;
                        }
                    }
                }

                $this->collAccounts = $collAccounts;
                $this->collAccountsPartial = false;
            }
        }

        return $this->collAccounts;
    }

    /**
     * Sets a collection of Account objects related by a many-to-many relationship
     * to the current object by way of the user_accounts cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection          $accounts A Propel collection.
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return $this|ChildUser The current object (for fluent API support)
     */
    public function setAccounts(Collection $accounts, ConnectionInterface $con = null)
    {
        $this->clearAccounts();
        $currentAccounts = $this->getAccounts();

        $accountsScheduledForDeletion = $currentAccounts->diff($accounts);

        foreach ($accountsScheduledForDeletion as $toDelete) {
            $this->removeAccount($toDelete);
        }

        foreach ($accounts as $account) {
            if (!$currentAccounts->contains($account)) {
                $this->doAddAccount($account);
            }
        }

        $this->collAccountsPartial = false;
        $this->collAccounts = $accounts;

        return $this;
    }

    /**
     * Gets the number of Account objects related by a many-to-many relationship
     * to the current object by way of the user_accounts cross-reference table.
     *
     * @param Criteria            $criteria Optional query object to filter the query
     * @param bool                $distinct Set to true to force count distinct
     * @param ConnectionInterface $con      Optional connection object
     *
     * @return int the number of related Account objects
     */
    public function countAccounts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAccountsPartial && !$this->isNew();
        if (null === $this->collAccounts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccounts) {
                return 0;
            } else {
                if ($partial && !$criteria) {
                    return count($this->getAccounts());
                }

                $query = ChildAccountQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collAccounts);
        }
    }

    /**
     * Associate a ChildAccount to this object
     * through the user_accounts cross reference table.
     *
     * @param ChildAccount $account
     *
     * @return ChildUser The current object (for fluent API support)
     */
    public function addAccount(ChildAccount $account)
    {
        if ($this->collAccounts === null) {
            $this->initAccounts();
        }

        if (!$this->getAccounts()->contains($account)) {
            // only add it if the **same** object is not already associated
            $this->collAccounts->push($account);
            $this->doAddAccount($account);
        }

        return $this;
    }

    /**
     * @param ChildAccount $account
     */
    protected function doAddAccount(ChildAccount $account)
    {
        $userAccounts = new ChildUserAccounts();

        $userAccounts->setAccount($account);

        $userAccounts->setUser($this);

        $this->addUserAccounts($userAccounts);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$account->isUsersLoaded()) {
            $account->initUsers();
            $account->getUsers()->push($this);
        } elseif (!$account->getUsers()->contains($this)) {
            $account->getUsers()->push($this);
        }
    }

    /**
     * Remove account of this object
     * through the user_accounts cross reference table.
     *
     * @param ChildAccount $account
     *
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeAccount(ChildAccount $account)
    {
        if ($this->getAccounts()->contains($account)) {
            $userAccounts = new ChildUserAccounts();
            $userAccounts->setAccount($account);
            if ($account->isUsersLoaded()) {
                //remove the back reference if available
                $account->getUsers()->removeObject($this);
            }

            $userAccounts->setUser($this);
            $this->removeUserAccounts(clone $userAccounts);
            $userAccounts->clear();

            $this->collAccounts->remove($this->collAccounts->search($account));

            if (null === $this->accountsScheduledForDeletion) {
                $this->accountsScheduledForDeletion = clone $this->collAccounts;
                $this->accountsScheduledForDeletion->clear();
            }

            $this->accountsScheduledForDeletion->push($account);
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
        $this->id = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->email = null;
        $this->password_hash = null;
        $this->password_expire = null;
        $this->enable = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collUserAccountss) {
                foreach ($this->collUserAccountss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAccounts) {
                foreach ($this->collAccounts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collUserAccountss = null;
        $this->collAccounts = null;
    }

    /**
     * Return the string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserTableMap::DEFAULT_STRING_FORMAT);
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
