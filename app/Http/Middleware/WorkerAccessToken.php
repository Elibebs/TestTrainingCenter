<?php

namespace App\Http\Middleware;

use Closure;
use App\TestCenter\Repos\WorkerRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;

class WorkerAccessToken
{
    protected $workerRepo;
    protected $apiResponse;

    public function __construct(WorkerRepo $workerRepo, ApiResponse $apiResponse){
        $this->workerRepo = $workerRepo;
        $this->apiResponse = $apiResponse;
    }

    public function handle($request, Closure $next)
    {
        $accessToken = $request->headers->get('access-token');

        if($accessToken === null) {
            Log::warning("No access-token header values present, throwing forbidden...");
            // Throw Forbidden
            return $this->apiResponse->forbidden("access-token is unauthorized");
        }

        if(!$this->workerRepo->isAccessTokenValid($accessToken)) {
            Log::warning("access-token is invalid...");
            return $this->apiResponse->forbidden("access-token is invalid");
        }

        return $next($request);
    }
}
