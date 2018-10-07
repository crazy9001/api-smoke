<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 9/24/2018
 * Time: 2:50 PM
 */

namespace Vtv\Question\Providers;

use Illuminate\Support\ServiceProvider;
use Vtv\Question\Models\Answer;
use Vtv\Question\Models\Question;
use Vtv\Question\Models\Topic;
use Vtv\Question\Repositories\Eloquent\DbAnswerRepository;
use Vtv\Question\Repositories\Eloquent\DbQuestionRepository;
use Vtv\Question\Repositories\Eloquent\DbTopicRepository;
use Vtv\Question\Repositories\Interfaces\AnswerInterface;
use Vtv\Question\Repositories\Interfaces\QuestionInterface;
use Vtv\Question\Repositories\Interfaces\TopicInterface;

class QuestionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'question');
    }

    public function register()
    {
        $this->app->singleton(TopicInterface::class, function () {
            return new DbTopicRepository(new Topic());
        });
        $this->app->singleton(QuestionInterface::class, function () {
            return new DbQuestionRepository(new Question());
        });
        $this->app->singleton(AnswerInterface::class, function () {
            return new DbAnswerRepository(new Answer());
        });
    }
}