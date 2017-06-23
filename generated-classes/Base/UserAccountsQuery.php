<?php

namespace Base;

use Exception;
use Map\UserAccountsTableMap;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use UserAccounts as ChildUserAccounts;
use UserAccountsQuery as ChildUserAccountsQuery;

/**
 * Base class that represents a query for the 'user_accounts' table.
 *
 *
 *
 * @method     ChildUserAccountsQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method     ChildUserAccountsQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method     ChildUserAccountsQuery orderByAlias($order = Criteria::ASC) Order by the alias column
 * @method     ChildUserAccountsQuery groupByUserId() Group by the user_id column
 * @method     ChildUserAccountsQuery groupByAccountId() Group by the account_id column
 * @method     ChildUserAccountsQuery groupByAlias() Group by the alias column
 * @method     ChildUserAccountsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildUserAccountsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildUserAccountsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 * @method     ChildUserAccountsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildUserAccountsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildUserAccountsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 * @method     ChildUserAccountsQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method     ChildUserAccountsQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method     ChildUserAccountsQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 * @method     ChildUserAccountsQuery joinWithUser($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the User relation
 * @method     ChildUserAccountsQuery leftJoinWithUser() Adds a LEFT JOIN clause and with to the query using the User relation
 * @method     ChildUserAccountsQuery rightJoinWithUser() Adds a RIGHT JOIN clause and with to the query using the User relation
 * @method     ChildUserAccountsQuery innerJoinWithUser() Adds a INNER JOIN clause and with to the query using the User relation
 * @method     ChildUserAccountsQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method     ChildUserAccountsQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method     ChildUserAccountsQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 * @method     ChildUserAccountsQuery joinWithAccount($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Account relation
 * @method     ChildUserAccountsQuery leftJoinWithAccount() Adds a LEFT JOIN clause and with to the query using the Account relation
 * @method     ChildUserAccountsQuery rightJoinWithAccount() Adds a RIGHT JOIN clause and with to the query using the Account relation
 * @method     ChildUserAccountsQuery innerJoinWithAccount() Adds a INNER JOIN clause and with to the query using the Account relation
 * @method     \UserQuery|\AccountQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 * @method     ChildUserAccounts findOne(ConnectionInterface $con = null) Return the first ChildUserAccounts matching the query
 * @method     ChildUserAccounts findOneOrCreate(ConnectionInterface $con = null) Return the first ChildUserAccounts matching the query, or a new ChildUserAccounts object populated from the query conditions when no match is found
 * @method     ChildUserAccounts findOneByUserId(int $user_id) Return the first ChildUserAccounts filtered by the user_id column
 * @method     ChildUserAccounts findOneByAccountId(int $account_id) Return the first ChildUserAccounts filtered by the account_id column
 * @method     ChildUserAccounts findOneByAlias(string $alias) Return the first ChildUserAccounts filtered by the alias column *

 * @method     ChildUserAccounts requirePk($key, ConnectionInterface $con = null) Return the ChildUserAccounts by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUserAccounts requireOne(ConnectionInterface $con = null) Return the first ChildUserAccounts matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUserAccounts requireOneByUserId(int $user_id) Return the first ChildUserAccounts filtered by the user_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUserAccounts requireOneByAccountId(int $account_id) Return the first ChildUserAccounts filtered by the account_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUserAccounts requireOneByAlias(string $alias) Return the first ChildUserAccounts filtered by the alias column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUserAccounts[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildUserAccounts objects based on current ModelCriteria
 * @method     ChildUserAccounts[]|ObjectCollection findByUserId(int $user_id) Return ChildUserAccounts objects filtered by the user_id column
 * @method     ChildUserAccounts[]|ObjectCollection findByAccountId(int $account_id) Return ChildUserAccounts objects filtered by the account_id column
 * @method     ChildUserAccounts[]|ObjectCollection findByAlias(string $alias) Return ChildUserAccounts objects filtered by the alias column
 * @method     ChildUserAccounts[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class UserAccountsQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\UserAccountsQuery object.
     *
     * @param string $dbName     The database name
     * @param string $modelName  The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'money', $modelName = '\\UserAccounts', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildUserAccountsQuery object.
     *
     * @param string   $modelAlias The alias of a model in the query
     * @param Criteria $criteria   Optional Criteria to build the query from
     *
     * @return ChildUserAccountsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildUserAccountsQuery) {
            return $criteria;
        }
        $query = new ChildUserAccountsQuery();
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
     * @param array[$user_id, $account_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildUserAccounts|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UserAccountsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = UserAccountsTableMap::getInstanceFromPool(serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]))))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUserAccounts A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT user_id, account_id, alias FROM user_accounts WHERE user_id = :p0 AND account_id = :p1';
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
            /** @var ChildUserAccounts $obj */
            $obj = new ChildUserAccounts();
            $obj->hydrate($row);
            UserAccountsTableMap::addInstanceToPool($obj, serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildUserAccounts|array|mixed the result, formatted by the current formatter
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
     * </code>.
     *
     * @param array               $keys Primary keys to use for the query
     * @param ConnectionInterface $con  an optional connection object
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
     * Filter the query by primary key.
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys.
     *
     * @param array $keys The list of primary key to use for the query
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(UserAccountsTableMap::COL_USER_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(UserAccountsTableMap::COL_ACCOUNT_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the user_id column.
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param mixed  $userId     The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the account_id column.
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id > 12
     * </code>
     *
     * @see       filterByAccount()
     *
     * @param mixed  $accountId  The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the alias column.
     *
     * Example usage:
     * <code>
     * $query->filterByAlias('fooValue');   // WHERE alias = 'fooValue'
     * $query->filterByAlias('%fooValue%', Criteria::LIKE); // WHERE alias LIKE '%fooValue%'
     * </code>
     *
     * @param string $alias      The value to use as filter.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByAlias($alias = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($alias)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserAccountsTableMap::COL_ALIAS, $alias, $comparison);
    }

    /**
     * Filter the query by a related \User object.
     *
     * @param \User|ObjectCollection $user       The related object(s) to use as filter
     * @param string                 $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof \User) {
            return $this
                ->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserAccountsTableMap::COL_USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type \User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation.
     *
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

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
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object.
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                              to be used as main alias in the secondary query
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\UserQuery');
    }

    /**
     * Filter the query by a related \Account object.
     *
     * @param \Account|ObjectCollection $account    The related object(s) to use as filter
     * @param string                    $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildUserAccountsQuery The current query, for fluid interface
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof \Account) {
            return $this
                ->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserAccountsTableMap::COL_ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAccount() only accepts arguments of type \Account or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Account relation.
     *
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function joinAccount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Account');

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
            $this->addJoinObject($join, 'Account');
        }

        return $this;
    }

    /**
     * Use the Account relation Account object.
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                              to be used as main alias in the secondary query
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\AccountQuery');
    }

    /**
     * Exclude object from result.
     *
     * @param ChildUserAccounts $userAccounts Object to remove from the list of results
     *
     * @return $this|ChildUserAccountsQuery The current query, for fluid interface
     */
    public function prune($userAccounts = null)
    {
        if ($userAccounts) {
            $this->addCond('pruneCond0', $this->getAliasedColName(UserAccountsTableMap::COL_USER_ID), $userAccounts->getUserId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(UserAccountsTableMap::COL_ACCOUNT_ID), $userAccounts->getAccountId(), Criteria::NOT_EQUAL);
            $this->combine(['pruneCond0', 'pruneCond1'], Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the user_accounts table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserAccountsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            UserAccountsTableMap::clearInstancePool();
            UserAccountsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     *
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *             if supported by native driver or if emulated using Propel.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserAccountsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(UserAccountsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            UserAccountsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            UserAccountsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }
} // UserAccountsQuery
