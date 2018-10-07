<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 2:57 PM
 */

namespace Vtv\Question\Repositories\Eloquent;


use Vtv\Base\Repositories\Eloquent\RepositoriesAbstract;
use Vtv\Question\Repositories\Interfaces\QuestionInterface;

class DbQuestionRepository extends RepositoriesAbstract implements QuestionInterface
{
    public function getPublicListQuestion(array $filters)
    {
        $query = $this->getModel()
            ->with(['answer'])
            ->where('status', '=', 1)
            ->limit($filters['limit'])
            ->offset($filters['offset']);
        return $query;
    }
}