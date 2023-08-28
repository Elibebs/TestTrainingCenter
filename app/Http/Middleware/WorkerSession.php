<?php

namespace App\Http\Middleware;

use Closure;
use App\TestCenter\Repos\WorkerRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;

class WorkerSession
{
    protected $workerRepo;
    protected $apiResponse;

    public function __construct(WorkerRepo $workerRepo, ApiResponse $apiResponse){
        $this->workerRepo = $workerRepo;
        $this->apiResponse = $apiResponse;
    }

    public function handle($request, Closure $next)
    {
        $sessionId = $request->headers->get('session-id');

        if($sessionId === null) {
            Log::warning("No session-id header values present, throwing forbidden...");
            // Throw Forbidden
            return $this->apiResponse->forbidden("session-id is unauthorized");
        }

        if(!$this->workerRepo->isSessionIdValid($sessionId)) {
            Log::warning("session-id is invalid...");
            // Throw Forbidden
            return $this->apiResponse->forbidden("session-id is unauthorized");
        }

        return $next($request);
    }
}
