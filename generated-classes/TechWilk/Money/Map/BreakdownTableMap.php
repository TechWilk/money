<?php

namespace TechWilk\Money\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use TechWilk\Money\Breakdown;
use TechWilk\Money\BreakdownQuery;


/**
 * This class defines the structure of the 'breakdown' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class BreakdownTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'TechWilk.Money.Map.BreakdownTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'money';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'breakdown';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\TechWilk\\Money\\Breakdown';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'TechWilk.Money.Breakdown';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
     * the column name for the id field
     */
    const COL_ID = 'breakdown.id';

    /**
     * the column name for the transaction_id field
     */
    const COL_TRANSACTION_ID = 'breakdown.transaction_id';

    /**
     * the column name for the description field
     */
    const COL_DESCRIPTION = 'breakdown.description';

    /**
     * the column name for the value field
     */
    const COL_VALUE = 'breakdown.value';

    /**
     * the column name for the category_id field
     */
    const COL_CATEGORY_ID = 'breakdown.category_id';

    /**
     * the column name for the created field
     */
    const COL_CREATED = 'breakdown.created';

    /**
     * the column name for the updated field
     */
    const COL_UPDATED = 'breakdown.updated';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'TransactionId', 'Description', 'Value', 'CategoryId', 'Created', 'Updated', ),
        self::TYPE_CAMELNAME     => array('id', 'transactionId', 'description', 'value', 'categoryId', 'created', 'updated', ),
        self::TYPE_COLNAME       => array(BreakdownTableMap::COL_ID, BreakdownTableMap::COL_TRANSACTION_ID, BreakdownTableMap::COL_DESCRIPTION, BreakdownTableMap::COL_VALUE, BreakdownTableMap::COL_CATEGORY_ID, BreakdownTableMap::COL_CREATED, BreakdownTableMap::COL_UPDATED, ),
        self::TYPE_FIELDNAME     => array('id', 'transaction_id', 'description', 'value', 'category_id', 'created', 'updated', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'TransactionId' => 1, 'Description' => 2, 'Value' => 3, 'CategoryId' => 4, 'Created' => 5, 'Updated' => 6, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'transactionId' => 1, 'description' => 2, 'value' => 3, 'categoryId' => 4, 'created' => 5, 'updated' => 6, ),
        self::TYPE_COLNAME       => array(BreakdownTableMap::COL_ID => 0, BreakdownTableMap::COL_TRANSACTION_ID => 1, BreakdownTableMap::COL_DESCRIPTION => 2, BreakdownTableMap::COL_VALUE => 3, BreakdownTableMap::COL_CATEGORY_ID => 4, BreakdownTableMap::COL_CREATED => 5, BreakdownTableMap::COL_UPDATED => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'transaction_id' => 1, 'description' => 2, 'value' => 3, 'category_id' => 4, 'created' => 5, 'updated' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('breakdown');
        $this->setPhpName('Breakdown');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\TechWilk\\Money\\Breakdown');
        $this->setPackage('TechWilk.Money');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('transaction_id', 'TransactionId', 'INTEGER', 'transaction', 'id', true, null, null);
        $this->addColumn('description', 'Description', 'VARCHAR', true, 50, null);
        $this->addColumn('value', 'Value', 'FLOAT', true, null, null);
        $this->addForeignKey('category_id', 'CategoryId', 'INTEGER', 'category', 'id', true, null, null);
        $this->addColumn('created', 'Created', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated', 'Updated', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Transaction', '\\TechWilk\\Money\\Transaction', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':transaction_id',
    1 => ':id',
  ),
), null, null, null, false);
        $this->addRelation('Category', '\\TechWilk\\Money\\Category', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':category_id',
    1 => ':id',
  ),
), null, null, null, false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created', 'update_column' => 'updated', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? BreakdownTableMap::CLASS_DEFAULT : BreakdownTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Breakdown object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = BreakdownTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = BreakdownTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + BreakdownTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = BreakdownTableMap::OM_CLASS;
            /** @var Breakdown $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            BreakdownTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = BreakdownTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = BreakdownTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Breakdown $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                BreakdownTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(BreakdownTableMap::COL_ID);
            $criteria->addSelectColumn(BreakdownTableMap::COL_TRANSACTION_ID);
            $criteria->addSelectColumn(BreakdownTableMap::COL_DESCRIPTION);
            $criteria->addSelectColumn(BreakdownTableMap::COL_VALUE);
            $criteria->addSelectColumn(BreakdownTableMap::COL_CATEGORY_ID);
            $criteria->addSelectColumn(BreakdownTableMap::COL_CREATED);
            $criteria->addSelectColumn(BreakdownTableMap::COL_UPDATED);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.transaction_id');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.value');
            $criteria->addSelectColumn($alias . '.category_id');
            $criteria->addSelectColumn($alias . '.created');
            $criteria->addSelectColumn($alias . '.updated');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(BreakdownTableMap::DATABASE_NAME)->getTable(BreakdownTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(BreakdownTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(BreakdownTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new BreakdownTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Breakdown or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Breakdown object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BreakdownTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \TechWilk\Money\Breakdown) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(BreakdownTableMap::DATABASE_NAME);
            $criteria->add(BreakdownTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = BreakdownQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            BreakdownTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                BreakdownTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the breakdown table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return BreakdownQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Breakdown or Criteria object.
     *
     * @param mixed               $criteria Criteria or Breakdown object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BreakdownTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Breakdown object
        }

        if ($criteria->containsKey(BreakdownTableMap::COL_ID) && $criteria->keyContainsValue(BreakdownTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.BreakdownTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = BreakdownQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // BreakdownTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BreakdownTableMap::buildTableMap();
