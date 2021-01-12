<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SortCriteria.
 */
class SortCriteria implements CriteriaInterface
{
    /**
     * @var options
     */
    protected $options;

    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * Apply criteria in query repository.
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $query = $model->newQuery();

        if (empty($this->options)) {
            $query->orderBy('created_at', 'desc');
        } else {
            foreach ($this->options as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }

        return $query;
    }
}
