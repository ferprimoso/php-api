<?php

namespace app\services;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class SortingService 
{
    /**
     * Apply sorting to the query based on the provided sort parameter.
     *
     * @param ActiveQuery $query The query object to which sorting should be applied.
     * @param string $sort The sorting parameter (e.g., "name:asc,publication_date:desc").
     * @param array $columns The valid columns for sorting.
     * @throws InvalidConfigException If an invalid sort column is provided.
     */
    public function applySorting(ActiveQuery $query, $sort, array $columns)
    {
        if (isset($sort)) {
            foreach (explode(',', $sort) as $sortPart) {
                $sortPart = trim($sortPart);
                $direction = SORT_ASC;
                if (strpos($sortPart, ':') !== false) {
                    list($column, $dir) = explode(':', $sortPart);
                    $column = trim($column);
                    $dir = trim(strtoupper($dir));
                    if ($dir === 'DESC') {
                        $direction = SORT_DESC;
                    } elseif ($dir !== 'ASC') {
                        $direction = SORT_ASC;
                    }
                } else {
                    $column = trim($sortPart);
                }

                if (in_array($column, $columns)) {
                    $query->addOrderBy([$column => $direction]);
                } else {
                    throw new InvalidConfigException("Invalid sort column: $column");
                }
            }
        }
    }
}
