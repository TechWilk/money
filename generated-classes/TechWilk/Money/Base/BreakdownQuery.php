<?php

namespace TechWilk\Money\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use TechWilk\Money\Breakdown as ChildBreakdown;
use TechWilk\Money\BreakdownQuery as ChildBreakdownQuery;
use TechWilk\Money\Map\BreakdownTableMap;

/**
 * Base class that represents a query for the 'breakdown' table.
 *
 *
 *
 * @method     ChildBreakdownQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildBreakdownQuery orderByTransactionId($order = Criteria::ASC) Order by the transaction_id column
 * @method     ChildBreakdownQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ChildBreakdownQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method     ChildBreakdownQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 *
 * @method     ChildBreakdownQuery groupById() Group by the id column
 * @method     ChildBreakdownQuery groupByTransactionId() Group by the transaction_id column
 * @method     ChildBreakdownQuery groupByDescription() Group by the description column
 * @method     ChildBreakdownQuery groupByValue() Group by the value column
 * @method     ChildBreakdownQuery groupByCategoryId() Group by the category_id column
 *
 * @method     ChildBreakdownQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBreakdownQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBreakdownQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBreakdownQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildBreakdownQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildBreakdownQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildBreakdownQuery leftJoinTransaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the Transaction relation
 * @method     ChildBreakdownQuery rightJoinTransaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Transaction relation
 * @method     ChildBreakdownQuery innerJoinTransaction($relationAlias = null) Adds a INNER JOIN clause to the query using the Transaction relation
 *
 * @method     ChildBreakdownQuery joinWithTransaction($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Transaction relation
 *
 * @method     ChildBreakdownQuery leftJoinWithTransaction() Adds a LEFT JOIN clause and with to the query using the Transaction relation
 * @method     ChildBreakdownQuery rightJoinWithTransaction() Adds a RIGHT JOIN clause and with to the query using the Transaction relation
 * @method     ChildBreakdownQuery innerJoinWithTransaction() Adds a INNER JOIN clause and with to the query using the Transaction relation
 *
 * @method     ChildBreakdownQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method     ChildBreakdownQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method     ChildBreakdownQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method     ChildBreakdownQuery joinWithCategory($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Category relation
 *
 * @method     ChildBreakdownQuery leftJoinWithCategory() Adds a LEFT JOIN clause and with to the query using the Category relation
 * @method     ChildBreakdownQuery rightJoinWithCategory() Adds a RIGHT JOIN clause and with to the query using the Category relation
 * @method     ChildBreakdownQuery innerJoinWithCategory() Adds a INNER JOIN clause and with to the query using the Category relation
 *
 * @method     \TechWilk\Money\TransactionQuery|\TechWilk\Money\CategoryQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildBreakdown findOne(ConnectionInterface $con = null) Return the first ChildBreakdown matching the query
 * @method     ChildBreakdown findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBreakdown matching the query, or a new ChildBreakdown object populated from the query conditions when no match is found
 *
 * @method     ChildBreakdown findOneById(int $id) Return the first ChildBreakdown filtered by the id column
 * @method     ChildBreakdown findOneByTransactionId(int $transaction_id) Return the first ChildBreakdown filtered by the transaction_id column
 * @method     ChildBreakdown findOneByDescription(string $description) Return the first ChildBreakdown filtered by the description column
 * @method     ChildBreakdown findOneByValue(double $value) Return the first ChildBreakdown filtered by the value column
 * @method     ChildBreakdown findOneByCategoryId(int $category_id) Return the first ChildBreakdown filtered by the category_id column *

 * @method     ChildBreakdown requirePk($key, ConnectionInterface $con = null) Return the ChildBreakdown by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBreakdown requireOne(ConnectionInterface $con = null) Return the first ChildBreakdown matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildBreakdown requireOneById(int $id) Return the first ChildBreakdown filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBreakdown requireOneByTransactionId(int $transaction_id) Return the first ChildBreakdown filtered by the transaction_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBreakdown requireOneByDescription(string $description) Return the first ChildBreakdown filtered by the description column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBreakdown requireOneByValue(double $value) Return the first ChildBreakdown filtered by the value column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildBreakdown requireOneByCategoryId(int $category_id) Return the first ChildBreakdown filtered by the category_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildBreakdown[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildBreakdown objects based on current ModelCriteria
 * @method     ChildBreakdown[]|ObjectCollection findById(int $id) Return ChildBreakdown objects filtered by the id column
 * @method     ChildBreakdown[]|ObjectCollection findByTransactionId(int $transaction_id) Return ChildBreakdown objects filtered by the transaction_id column
 * @method     ChildBreakdown[]|ObjectCollection findByDescription(string $description) Return ChildBreakdown objects filtered by the description column
 * @method     ChildBreakdown[]|ObjectCollection findByValue(double $value) Return ChildBreakdown objects filtered by the value column
 * @method     ChildBreakdown[]|ObjectCollection findByCategoryId(int $category_id) Return ChildBreakdown objects filtered by the category_id column
 * @method     ChildBreakdown[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class BreakdownQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \TechWilk\Money\Base\BreakdownQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'money', $modelName = '\\TechWilk\\Money\\Breakdown', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBreakdownQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBreakdownQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildBreakdownQuery) {
            return $criteria;
        }
        $query = new ChildBreakdownQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildBreakdown|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(BreakdownTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = BreakdownTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildBreakdown A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, transaction_id, description, value, category_id FROM breakdown WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildBreakdown $obj */
            $obj = new ChildBreakdown();
            $obj->hydrate($row);
            BreakdownTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildBreakdown|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BreakdownTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BreakdownTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreakdownTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTransactionId(1234); // WHERE transaction_id = 1234
     * $query->filterByTransactionId(array(12, 34)); // WHERE transaction_id IN (12, 34)
     * $query->filterByTransactionId(array('min' => 12)); // WHERE transaction_id > 12
     * </code>
     *
     * @see       filterByTransaction()
     *
     * @param     mixed $transactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByTransactionId($transactionId = null, $comparison = null)
    {
        if (is_array($transactionId)) {
            $useMinMax = false;
            if (isset($transactionId['min'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_TRANSACTION_ID, $transactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($transactionId['max'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_TRANSACTION_ID, $transactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreakdownTableMap::COL_TRANSACTION_ID, $transactionId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%', Criteria::LIKE); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreakdownTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value)) {
            $useMinMax = false;
            if (isset($value['min'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_VALUE, $value['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($value['max'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_VALUE, $value['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreakdownTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id > 12
     * </code>
     *
     * @see       filterByCategory()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(BreakdownTableMap::COL_CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BreakdownTableMap::COL_CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query by a related \TechWilk\Money\Transaction object
     *
     * @param \TechWilk\Money\Transaction|ObjectCollection $transaction The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByTransaction($transaction, $comparison = null)
    {
        if ($transaction instanceof \TechWilk\Money\Transaction) {
            return $this
                ->addUsingAlias(BreakdownTableMap::COL_TRANSACTION_ID, $transaction->getId(), $comparison);
        } elseif ($transaction instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BreakdownTableMap::COL_TRANSACTION_ID, $transaction->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTransaction() only accepts arguments of type \TechWilk\Money\Transaction or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Transaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function joinTransaction($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Transaction');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Transaction');
        }

        return $this;
    }

    /**
     * Use the Transaction relation Transaction object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \TechWilk\Money\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useTransactionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTransaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Transaction', '\TechWilk\Money\TransactionQuery');
    }

    /**
     * Filter the query by a related \TechWilk\Money\Category object
     *
     * @param \TechWilk\Money\Category|ObjectCollection $category The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildBreakdownQuery The current query, for fluid interface
     */
    public function filterByCategory($category, $comparison = null)
    {
        if ($category instanceof \TechWilk\Money\Category) {
            return $this
                ->addUsingAlias(BreakdownTableMap::COL_CATEGORY_ID, $category->getId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BreakdownTableMap::COL_CATEGORY_ID, $category->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategory() only accepts arguments of type \TechWilk\Money\Category or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Category relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Category');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Category');
        }

        return $this;
    }

    /**
     * Use the Category relation Category object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \TechWilk\Money\CategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\TechWilk\Money\CategoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBreakdown $breakdown Object to remove from the list of results
     *
     * @return $this|ChildBreakdownQuery The current query, for fluid interface
     */
    public function prune($breakdown = null)
    {
        if ($breakdown) {
            $this->addUsingAlias(BreakdownTableMap::COL_ID, $breakdown->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the breakdown table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BreakdownTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            BreakdownTableMap::clearInstancePool();
            BreakdownTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BreakdownTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(BreakdownTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            BreakdownTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            BreakdownTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // BreakdownQuery
