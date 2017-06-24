<?php

namespace TechWilk\Money\Map;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use Propel\Runtime\Propel;
use TechWilk\Money\Hashtag;
use TechWilk\Money\HashtagQuery;

/**
 * This class defines the structure of the 'hashtag' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 */
class HashtagTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class.
     */
    const CLASS_NAME = 'TechWilk.Money.Map.HashtagTableMap';

    /**
     * The default database name for this class.
     */
    const DATABASE_NAME = 'money';

    /**
     * The table name for this class.
     */
    const TABLE_NAME = 'hashtag';

    /**
     * The related Propel class for this table.
     */
    const OM_CLASS = '\\TechWilk\\Money\\Hashtag';

    /**
     * A class that can be returned by this tableMap.
     */
    const CLASS_DEFAULT = 'TechWilk.Money.Hashtag';

    /**
     * The total number of columns.
     */
    const NUM_COLUMNS = 2;

    /**
     * The number of lazy-loaded columns.
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS).
     */
    const NUM_HYDRATE_COLUMNS = 2;

    /**
     * the column name for the id field.
     */
    const COL_ID = 'hashtag.id';

    /**
     * the column name for the tag field.
     */
    const COL_TAG = 'hashtag.tag';

    /**
     * The default string format for model objects of the related table.
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames.
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = [
        self::TYPE_PHPNAME       => ['Id', 'Tag'],
        self::TYPE_CAMELNAME     => ['id', 'tag'],
        self::TYPE_COLNAME       => [self::COL_ID, self::COL_TAG],
        self::TYPE_FIELDNAME     => ['id', 'tag'],
        self::TYPE_NUM           => [0, 1],
    ];

    /**
     * holds an array of keys for quick access to the fieldnames array.
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = [
        self::TYPE_PHPNAME       => ['Id' => 0, 'Tag' => 1],
        self::TYPE_CAMELNAME     => ['id' => 0, 'tag' => 1],
        self::TYPE_COLNAME       => [self::COL_ID => 0, self::COL_TAG => 1],
        self::TYPE_FIELDNAME     => ['id' => 0, 'tag' => 1],
        self::TYPE_NUM           => [0, 1],
    ];

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded.
     *
     * @throws PropelException
     *
     * @return void
     */
    public function initialize()
    {
        // attributes
        $this->setName('hashtag');
        $this->setPhpName('Hashtag');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\TechWilk\\Money\\Hashtag');
        $this->setPackage('TechWilk.Money');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('tag', 'Tag', 'VARCHAR', true, 50, null);
    }

 // initialize()

    /**
     * Build the RelationMap objects for this table relationships.
     */
    public function buildRelations()
    {
        $this->addRelation('TransactionHashtag', '\\TechWilk\\Money\\TransactionHashtag', RelationMap::ONE_TO_MANY, [
  0 => [
    0 => ':hashtag_id',
    1 => ':id',
  ],
], null, null, 'TransactionHashtags', false);
        $this->addRelation('Transaction', '\\TechWilk\\Money\\Transaction', RelationMap::MANY_TO_MANY, [], null, null, 'Transactions');
    }

 // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                          TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return;
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
     *                          TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
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
     * @param bool $withPrefix Whether or not to return the path with the class name
     *
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? self::CLASS_DEFAULT : self::OM_CLASS;
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
     *
     * @return array (Hashtag object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = self::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = self::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + self::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = self::OM_CLASS;
            /** @var Hashtag $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            self::addInstanceToPool($obj, $key);
        }

        return [$obj, $col];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     *
     * @return array
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = [];

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = self::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = self::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Hashtag $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                self::addInstanceToPool($obj, $key);
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
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(self::COL_ID);
            $criteria->addSelectColumn(self::COL_TAG);
        } else {
            $criteria->addSelectColumn($alias.'.id');
            $criteria->addSelectColumn($alias.'.tag');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     *
     * @return TableMap
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(self::DATABASE_NAME);
        if (!$dbMap->hasTable(self::TABLE_NAME)) {
            $dbMap->addTableObject(new self());
        }
    }

     /**
      * Performs a DELETE on the database, given a Hashtag or Criteria object OR a primary key value.
      *
      * @param mixed               $values Criteria or Hashtag object or primary key or array of primary keys
      *              which is used to create the DELETE statement
      * @param  ConnectionInterface $con the connection to use
      *
      * @throws PropelException Any exceptions caught during processing will be
      *                         rethrown wrapped into a PropelException.
      *
      * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
      *                         if supported by native driver or if emulated using Propel.
      */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
         if (null === $con) {
             $con = Propel::getServiceContainer()->getWriteConnection(self::DATABASE_NAME);
         }

         if ($values instanceof Criteria) {
             // rename for clarity
            $criteria = $values;
         } elseif ($values instanceof \TechWilk\Money\Hashtag) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
         } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(self::DATABASE_NAME);
             $criteria->add(self::COL_ID, (array) $values, Criteria::IN);
         }

         $query = HashtagQuery::create()->mergeWith($criteria);

         if ($values instanceof Criteria) {
             self::clearInstancePool();
         } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                self::removeInstanceFromPool($singleval);
            }
         }

         return $query->delete($con);
     }

    /**
     * Deletes all rows from the hashtag table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return HashtagQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Hashtag or Criteria object.
     *
     * @param mixed               $criteria Criteria or Hashtag object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con      the ConnectionInterface connection to use
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     *
     * @return mixed The new primary key.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(self::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Hashtag object
        }

        if ($criteria->containsKey(self::COL_ID) && $criteria->keyContainsValue(self::COL_ID)) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.self::COL_ID.')');
        }

        // Set the correct dbName
        $query = HashtagQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }
} // HashtagTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
HashtagTableMap::buildTableMap();
