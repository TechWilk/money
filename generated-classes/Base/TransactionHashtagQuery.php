<?php

namespace Base;

use \TransactionHashtag as ChildTransactionHashtag;
use \TransactionHashtagQuery as ChildTransactionHashtagQuery;
use \Exception;
use \PDO;
use Map\TransactionHashtagTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'transaction_hashtag' table.
 *
 *
 *
 * @method     ChildTransactionHashtagQuery orderByTransactionId($order = Criteria::ASC) Order by the transaction_id column
 * @method     ChildTransactionHashtagQuery orderByHashtagId($order = Criteria::ASC) Order by the hashtag_id column
 *
 * @method     ChildTransactionHashtagQuery groupByTransactionId() Group by the transaction_id column
 * @method     ChildTransactionHashtagQuery groupByHashtagId() Group by the hashtag_id column
 *
 * @method     ChildTransactionHashtagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTransactionHashtagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTransactionHashtagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTransactionHashtagQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildTransactionHashtagQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildTransactionHashtagQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildTransactionHashtagQuery leftJoinTransaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the Transaction relation
 * @method     ChildTransactionHashtagQuery rightJoinTransaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Transaction relation
 * @method     ChildTransactionHashtagQuery innerJoinTransaction($relationAlias = null) Adds a INNER JOIN clause to the query using the Transaction relation
 *
 * @method     ChildTransactionHashtagQuery joinWithTransaction($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Transaction relation
 *
 * @method     ChildTransactionHashtagQuery leftJoinWithTransaction() Adds a LEFT JOIN clause and with to the query using the Transaction relation
 * @method     ChildTransactionHashtagQuery rightJoinWithTransaction() Adds a RIGHT JOIN clause and with to the query using the Transaction relation
 * @method     ChildTransactionHashtagQuery innerJoinWithTransaction() Adds a INNER JOIN clause and with to the query using the Transaction relation
 *
 * @method     ChildTransactionHashtagQuery leftJoinHashtag($relationAlias = null) Adds a LEFT JOIN clause to the query using the Hashtag relation
 * @method     ChildTransactionHashtagQuery rightJoinHashtag($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Hashtag relation
 * @method     ChildTransactionHashtagQuery innerJoinHashtag($relationAlias = null) Adds a INNER JOIN clause to the query using the Hashtag relation
 *
 * @method     ChildTransactionHashtagQuery joinWithHashtag($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Hashtag relation
 *
 * @method     ChildTransactionHashtagQuery leftJoinWithHashtag() Adds a LEFT JOIN clause and with to the query using the Hashtag relation
 * @method     ChildTransactionHashtagQuery rightJoinWithHashtag() Adds a RIGHT JOIN clause and with to the query using the Hashtag relation
 * @method     ChildTransactionHashtagQuery innerJoinWithHashtag() Adds a INNER JOIN clause and with to the query using the Hashtag relation
 *
 * @method     \TransactionQuery|\HashtagQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildTransactionHashtag findOne(ConnectionInterface $con = null) Return the first ChildTransactionHashtag matching the query
 * @method     ChildTransactionHashtag findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTransactionHashtag matching the query, or a new ChildTransactionHashtag object populated from the query conditions when no match is found
 *
 * @method     ChildTransactionHashtag findOneByTransactionId(int $transaction_id) Return the first ChildTransactionHashtag filtered by the transaction_id column
 * @method     ChildTransactionHashtag findOneByHashtagId(int $hashtag_id) Return the first ChildTransactionHashtag filtered by the hashtag_id column *

 * @method     ChildTransactionHashtag requirePk($key, ConnectionInterface $con = null) Return the ChildTransactionHashtag by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTransactionHashtag requireOne(ConnectionInterface $con = null) Return the first ChildTransactionHashtag matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTransactionHashtag requireOneByTransactionId(int $transaction_id) Return the first ChildTransactionHashtag filtered by the transaction_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTransactionHashtag requireOneByHashtagId(int $hashtag_id) Return the first ChildTransactionHashtag filtered by the hashtag_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTransactionHashtag[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTransactionHashtag objects based on current ModelCriteria
 * @method     ChildTransactionHashtag[]|ObjectCollection findByTransactionId(int $transaction_id) Return ChildTransactionHashtag objects filtered by the transaction_id column
 * @method     ChildTransactionHashtag[]|ObjectCollection findByHashtagId(int $hashtag_id) Return ChildTransactionHashtag objects filtered by the hashtag_id column
 * @method     ChildTransactionHashtag[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TransactionHashtagQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\TransactionHashtagQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'money', $modelName = '\\TransactionHashtag', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTransactionHashtagQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTransactionHashtagQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTransactionHashtagQuery) {
            return $criteria;
        }
        $query = new ChildTransactionHashtagQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$transaction_id, $hashtag_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildTransactionHashtag|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TransactionHashtagTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = TransactionHashtagTableMap::getInstanceFromPool(serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]))))) {
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
     * @return ChildTransactionHashtag A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT transaction_id, hashtag_id FROM transaction_hashtag WHERE transaction_id = :p0 AND hashtag_id = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildTransactionHashtag $obj */
            $obj = new ChildTransactionHashtag();
            $obj->hydrate($row);
            TransactionHashtagTableMap::addInstanceToPool($obj, serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]));
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
     * @return ChildTransactionHashtag|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(TransactionHashtagTableMap::COL_TRANSACTION_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(TransactionHashtagTableMap::COL_HASHTAG_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByTransactionId($transactionId = null, $comparison = null)
    {
        if (is_array($transactionId)) {
            $useMinMax = false;
            if (isset($transactionId['min'])) {
                $this->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $transactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($transactionId['max'])) {
                $this->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $transactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $transactionId, $comparison);
    }

    /**
     * Filter the query on the hashtag_id column
     *
     * Example usage:
     * <code>
     * $query->filterByHashtagId(1234); // WHERE hashtag_id = 1234
     * $query->filterByHashtagId(array(12, 34)); // WHERE hashtag_id IN (12, 34)
     * $query->filterByHashtagId(array('min' => 12)); // WHERE hashtag_id > 12
     * </code>
     *
     * @see       filterByHashtag()
     *
     * @param     mixed $hashtagId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByHashtagId($hashtagId = null, $comparison = null)
    {
        if (is_array($hashtagId)) {
            $useMinMax = false;
            if (isset($hashtagId['min'])) {
                $this->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $hashtagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hashtagId['max'])) {
                $this->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $hashtagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $hashtagId, $comparison);
    }

    /**
     * Filter the query by a related \Transaction object
     *
     * @param \Transaction|ObjectCollection $transaction The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByTransaction($transaction, $comparison = null)
    {
        if ($transaction instanceof \Transaction) {
            return $this
                ->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $transaction->getId(), $comparison);
        } elseif ($transaction instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionHashtagTableMap::COL_TRANSACTION_ID, $transaction->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTransaction() only accepts arguments of type \Transaction or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Transaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
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
     * @return \TransactionQuery A secondary query class using the current class as primary query
     */
    public function useTransactionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTransaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Transaction', '\TransactionQuery');
    }

    /**
     * Filter the query by a related \Hashtag object
     *
     * @param \Hashtag|ObjectCollection $hashtag The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function filterByHashtag($hashtag, $comparison = null)
    {
        if ($hashtag instanceof \Hashtag) {
            return $this
                ->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $hashtag->getId(), $comparison);
        } elseif ($hashtag instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionHashtagTableMap::COL_HASHTAG_ID, $hashtag->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByHashtag() only accepts arguments of type \Hashtag or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Hashtag relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function joinHashtag($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Hashtag');

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
            $this->addJoinObject($join, 'Hashtag');
        }

        return $this;
    }

    /**
     * Use the Hashtag relation Hashtag object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \HashtagQuery A secondary query class using the current class as primary query
     */
    public function useHashtagQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinHashtag($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Hashtag', '\HashtagQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTransactionHashtag $transactionHashtag Object to remove from the list of results
     *
     * @return $this|ChildTransactionHashtagQuery The current query, for fluid interface
     */
    public function prune($transactionHashtag = null)
    {
        if ($transactionHashtag) {
            $this->addCond('pruneCond0', $this->getAliasedColName(TransactionHashtagTableMap::COL_TRANSACTION_ID), $transactionHashtag->getTransactionId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(TransactionHashtagTableMap::COL_HASHTAG_ID), $transactionHashtag->getHashtagId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the transaction_hashtag table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TransactionHashtagTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TransactionHashtagTableMap::clearInstancePool();
            TransactionHashtagTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(TransactionHashtagTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TransactionHashtagTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TransactionHashtagTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TransactionHashtagTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // TransactionHashtagQuery
