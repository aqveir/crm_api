<?php

namespace Modules\Core\Repositories;

use Modules\Core\Contracts\Common\{RepositoryContract};

use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentRepository.
 */
abstract class EloquentRepository //implements RepositoryContract
{
    /**
     * The repository model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The query builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Alias for the query limit.
     *
     * @var int
     */
    protected $take;

    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Array of related models to get count.
     *
     * @var array
     */
    protected $withCount = [];

    /**
     * Array of one or more where clause parameters.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Array of one or more orWhere clause parameters.
     *
     * @var array
     */
    protected $orwheres = [];

    /**
     * Array of one or more where in clause parameters.
     *
     * @var array
     */
    protected $whereIns = [];

    /**
     * Array of one or more ORDER BY column/value pairs.
     *
     * @var array
     */
    protected $orderBys = [];

    /**
     * Array of scope methods to call on the model.
     *
     * @var array
     */
    protected $scopes = [];


    /**
     * Get all the model records in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        $this->newQuery()->eagerLoad();

        $models = $this->query->get();

        $this->unsetClauses();

        return $models;
    }


    /**
     * Count the number of specified model records in the database.
     *
     * @return int
     */
    public function count()
    {
        return $this->get()->count();
    }


    /**
     * Get the first/firstOrFail specified model record from the database.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first(bool $fail=false)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        //Check the condition for using right method
        if ($fail) {
            $model = $this->query->firstOrFail();
        } else {
            $model = $this->query->first();
        } //End if        

        $this->unsetClauses();

        return $model;
    }
    public function firstOrFail()
    {
        return $this->first(true);
    }


    /**
     * Get all the specified model records in the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->get();

        $this->unsetClauses();

        return $models;
    }


    /**
     * Get the specified model record from the database.
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->findOrFail($id);
    }


    /**
     * @param $item
     * @param $column
     * @param  array  $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByColumn($item, $column, array $columns = ['*'])
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->where($column, $item)->first($columns);
    }


    /**
     * @param $item
     * @param $column
     * @param  array  $columns
     *
     * @return boolean
     */
    public function exists($item, $column)
    {
        $objReturnValue = false;
        try {
            $model = $this->getByColumn($item, $column);
            $objReturnValue = ($model!=null);
        } catch(Exception $e) {
            
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Delete the specified model record from the database.
     *
     * @param $id
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteById($id, int $userId=null)
    {
        $this->unsetClauses();

        $query = $this->getById($id);
        $query['deleted_by'] = $userId;
        $query->save();

        return $query->delete();
    }


    /**
     * Set the query limit.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit($limit)
    {
        $this->take = $limit;

        return $this;
    }


    /**
     * Set an ORDER BY clause.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBys[] = compact('column', 'direction');

        return $this;
    }


    /**
     * @param int    $limit
     * @param array  $columns
     * @param string $pageName
     * @param null   $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->paginate($limit, $columns, $pageName, $page);

        $this->unsetClauses();

        return $models;
    }


    /**
     * Add a simple where clause to the query.
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     *
     * @return $this
     */
    public function where($column, $value, $operator = '=')
    {
        $this->wheres[] = compact('column', 'value', 'operator');

        return $this;
    }


    /**
     * Add a simple orWhere clause to the query.
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     *
     * @return $this
     */
    public function orWhere($column, $value, $operator = '=')
    {
        $this->orwheres[] = compact('column', 'value', 'operator');

        return $this;
    }


    /**
     * Add a simple where in clause to the query.
     *
     * @param string $column
     * @param mixed  $values
     *
     * @return $this
     */
    public function whereIn($column, $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->whereIns[] = compact('column', 'values');

        return $this;
    }


    /**
     * Set Eloquent relationships to eager load.
     *
     * @param $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->with = $relations;

        return $this;
    }


    /**
     * Get Eloquent relationships count.
     *
     * @param $relations
     *
     * @return $this
     */
    public function withCount($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->withCount = $relations;

        return $this;
    }


    /**
     * Create a new instance of the model's query builder.
     *
     * @return $this
     */
    protected function newQuery()
    {
        $this->query = $this->model->newQuery();

        return $this;
    }


    /**
     * Add relationships to the query builder to eager load.
     *
     * @return $this
     */
    protected function eagerLoad()
    {
        foreach ($this->with as $relation) {
            $this->query->with($relation);
        }

        foreach ($this->withCount as $relation) {
            $this->query->withCount($relation);
        }

        return $this;
    }


    /**
     * Set clauses on the query builder.
     *
     * @return $this
     */
    protected function setClauses()
    {
        foreach ($this->wheres as $where) {
            $this->query->where($where['column'], $where['operator'], $where['value']);
        }

        foreach ($this->orwheres as $orwhere) {
            $this->query->orWhere($orwhere['column'], $orwhere['operator'], $orwhere['value']);
        }

        foreach ($this->whereIns as $whereIn) {
            $this->query->whereIn($whereIn['column'], $whereIn['values']);
        }

        foreach ($this->orderBys as $orders) {
            $this->query->orderBy($orders['column'], $orders['direction']);
        }

        if (isset($this->take) and ($this->take>0)) {
            $this->query->take($this->take);
        }

        return $this;
    }


    /**
     * Set query scopes.
     *
     * @return $this
     */
    protected function setScopes()
    {
        foreach ($this->scopes as $method => $args) {
            $this->query->$method(implode(', ', $args));
        }

        return $this;
    }


    /**
     * Reset the query clause parameter arrays.
     *
     * @return $this
     */
    protected function unsetClauses()
    {
        $this->wheres = [];
        $this->orwheres = [];
        $this->whereIns = [];
        $this->scopes = [];
        $this->take = null;

        return $this;
    }


    /**
     * Create Object
     * 
     * @param array $attributes
     * @param int $userId (created_by)
     * @param string $ipAddress
     *
     * @return mixed
     */
    public function create(array $attributes, int $userId=null, string $ipAddress=null)
    {
        $model = $this->model->create($attributes);

        //Check if the model exists
        if (!empty($model)) {

            if ((!empty($userId)) || (!empty($ipAddress))) {
                if (!empty($userId)) {
                    $model['created_by'] = $userId;
                } //End if
                
                if (!empty($ipAddress)) {
                    $model['ip_address'] = $userId;
                } //End if

                $model->save();
            } //End if
        } //End if
        
        return $model;
    } //Function ends


    /**
     * Update Object
     * 
     * @param mixed $item
     * @param string $column
     * @param array $attributes
     * @param int $userId (updated_by)
     * @param string $ipAddress
     *
     * @return mixed
     */
    public function update($item, string $column='id', array $attributes=[], int $userId=null, string $ipAddress=null)
    {
        $model = $this->getByColumn($item, $column);

        if ($attributes && is_array($attributes) && count($attributes)>0) {
            $model->update($attributes);

            if ((!empty($userId)) || (!empty($ipAddress))) {
                if (!empty($userId)) {
                    $model['updated_by'] = $userId;
                } //End if
                
                if (!empty($ipAddress)) {
                    $model['ip_address'] = $userId;
                } //End if

                $model->save();
            } //End if
        } //End if

        return $model;
    } //Function ends


    /**
     * Delete Object
     * 
     * @param mixed $item
     * @param string $column
     * @param int $userId (deleted_by)
     *
     * @return mixed
     */
    public function delete($item, string $column='id', int $userId=null, string $ipAddress=null)
    {
        $model = $this->getByColumn($item, $column);

        //Check if the model exists
        if (!empty($model)) {
            if ((!empty($userId)) || (!empty($ipAddress))) {
                if (!empty($userId)) {
                    $model['deleted_by'] = $userId;
                } //End if
                
                if (!empty($ipAddress)) {
                    $model['ip_address'] = $userId;
                } //End if

                $model->save();
            } //End if

            //Delete
            $model->delete();            
        } //End if

        return $model;
    } //Function ends

} //Class ends