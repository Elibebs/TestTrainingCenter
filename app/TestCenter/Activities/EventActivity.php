<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\EventRepo;
use App\TestCenter\Repos\WorkerRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;
use App\TestCenter\Traits\WorkerTrait;


class EventActivity extends BaseActivity
{
    use WorkerTrait;

    protected $eventRepo;
    protected $apiResponse;
    protected $workerRepo;

    public function __construct(EventRepo $eventRepo, WorkerRepo $workerRepo, ApiResponse $apiResponse){
        $this->eventRepo = $eventRepo;
        $this->workerRepo = $workerRepo;
        $this->apiResponse = $apiResponse;
    }

    public function listEvents( $filters )
    {         
        $events = $this->eventRepo->listEvents( $filters );

        return $this->apiResponse->success("Events retrieved successfully", ["data" => $events]);
    }

    public function listEventsJson( $filters )
    {         
        $events = $this->eventRepo->listEventsJson( $filters );

        return response()->json($events, 200);
    }

    public function listUpcomingEvents()
    {
        $events = $this->eventRepo->listUpcomingEvents();

        return $this->apiResponse->success("Upcoming events retrieved successfully",  ["data" => $events->toArray()] );

    }


    public function viewEvent($data, $id)
    {

    }

    public function createEvent( $data )
    {
       
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['title', 'location', 'date', 'start_time', 'end_time', 'type'], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        $start_date = strtotime($data['date'] . $data['start_time']);
        $end_date = strtotime($data['date'] . $data['end_time']);

        $data['start'] = date('Y-m-d H:i:s', $start_date);
        $data['end'] = date('Y-m-d H:i:s', $end_date);

        if( $event = $this->eventRepo->createEvent( $data ) )
        {
            $message = "Event :  added successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $event->toArray()]);
        }
        else
        {
            $message = "Unable to add event";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->generalError($message);
        } 
    }

    public function updateEvent( $data, $id )
    {
        //Get event
        $event = $this->eventRepo->findEventById( $id );

        if( !$event )
        {
            return $this->apiResponse->notFoundError("Event not found");
        }

        if( $event = $this->eventRepo->updateEvent($data, $event) )
        {
            return $this->apiResponse->success("Event updated successfully", ["data" => $event]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to update event");
    }

    public function deleteEvent( $id )
    {
        $event = $this->eventRepo->findEventById( $id );

        if( !$event )
        {
            return $this->apiResponse->notFoundError("Event not found");
        }

        if( $event->delete() )
        {
            return $this->apiResponse->success("Event deleted successfully", ["data" => $event]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to delete event");
    }

    public function bookEvent( Array $data )
    {
        $worker = $this->getAuthWorker();

        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['event_id'], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        if( $worker->eventBookings && count($worker->eventBookings) > 0 )
        {
            return $this->apiResponse->generalError("You already have an event booked");
        }

        //Get event
        $event = $this->eventRepo->findEventById( $data['event_id'] );
        if( !$event )
        {
            return $this->apiResponse->notFoundError("Event not found");
        }

        if( $booking = $this->eventRepo->bookEvent( $event, $worker) )
        {
            $booking['event'] = $event;

            return $this->apiResponse->success("Event booked successfully", ["data" => $booking]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to book for event");
    }

}