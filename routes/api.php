<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//TestCenter APIs
Route::middleware(
    ['user.access_token', 'user.session_id']
)->group(function () {

});
Route::get('/worker/{id}/details', 'WorkerController@workerDetails')->name('worker.details');


Route::middleware(['cors'])->group(function () {
    //TEST TYPE
    Route::resource('/test/types', 'TestTypeController');
    
    //Test Grading
    Route::resource('/test/type/grades', 'TestGradeController');

    //Specialty
    Route::resource('/specialties', 'SpecialtyController');

    //TEST QUESTIONS
    Route::resource('/questions', 'TestQuestionController');
    Route::post('/tests/search/skills/list', 'TestQuestionController@searchSkillsList')->name("tests.search.skills");


    //PossibleAnswers
    Route::resource('/answers', 'AnswerController');
    
    //create and take a test
    Route::post('/create/test', 'testController@createTest')->name("test.create");
    Route::resource('/tests', 'TestController');
    Route::post('/tests/search', 'TestController@searchTests')->name("tests.search");
    Route::get('/tests/{id}/attempts', 'TestController@testAttempts')->name('test.attempts');

    //Training Types
    Route::resource('/training/types', 'TrainingTypeController');

    //Training Type Directories
    Route::resource('/training/type/directories', 'TrainingTypeDirectoryController');

    //Training Type Directories
    Route::resource('/training/resources', 'TrainingResourceController');

    //Events
    Route::resource('/training/events', 'EventController');
    Route::get('/events/list', 'EventController@getJsonEvents')->name('events.json');

    Route::post('/worker/register', 'WorkerController@register')->name('worker.register');
    Route::post('/worker/session', 'WorkerController@login')->name('worker.login');

    Route::middleware(['worker.access_token', 'worker.session_id'])->group(function () {
        Route::get('/worker/test/types', 'WorkerController@listTestTypes')->name('worker.test.types');
        Route::get('/worker/test/questions', 'WorkerController@listTestQuestions')->name('worker.test.questions');
        Route::post('/worker/test/start', 'WorkerController@startTest')->name('worker.test.start');
        Route::post('/worker/test/question/answer', 'WorkerController@questionAnswer')->name('worker.test.question.answer');
        Route::post('/worker/test/end', 'WorkerController@endTest')->name('worker.test.end');
        Route::post('/worker/test/retake', 'WorkerController@retakeTest')->name('worker.test.retake');
        Route::get('/worker/tests', 'WorkerController@listTests')->name('worker.test.list');
        Route::post('/worker/test/image/upload', 'WorkerController@uploadImage')->name('worker.test.image.upload');
        Route::get('/worker/training/types', 'WorkerController@listTrainingTypes')->name('worker.training.types');
        Route::get('/worker/training/resources', 'WorkerController@listTrainingResources')->name('worker.training.resources');
        Route::post('/worker/resource/view', 'WorkerController@viewResource')->name('worker.training.resources.views');
        Route::get('/worker/training/upcoming/events', 'EventController@upcomingEvents')->name('worker.training.upcoming.events');
        Route::post('/worker/training/events/book', 'EventController@bookEvent')->name('worker.training.events.book');

    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
