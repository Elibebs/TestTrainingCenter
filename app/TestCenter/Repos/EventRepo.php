<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use App\Models\WorkerEventBooking;

use App\TestCenter\Utilities\Constants;



class EventRepo
{

    public function listEvents( $filters )
    {
        $pageSize = $filters['pageSize'] ?? 1000;
        $predicate = Event::query();
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }

            $predicate->where($key, $filter);
        }

        return $predicate->orderBy('created_at', 'DESC')->paginate($pageSize);
    }

    public function listEventsJson( $filters )
    {
        $pageSize = $filters['pageSize'] ?? 1000;
        $predicate = Event::query();

        if( isset($filters['type']) )
        {
            $predicate->where( 'type', $filters['type'] );
        }

        if( isset($filters['start']) && isset($filters['end']) )
        {
            $predicate->whereDate('start', '>=', $filters['start'])
                    ->whereDate('end', '<=', $filters['end']);
        }

        return $predicate->orderBy('created_at', 'DESC')->get();
    }

    public function listUpcomingEvents()
    {
        return Event::whereDate('date', '>=', Carbon::now())
                ->orderBy('date', 'ASC')
                ->get(['id', 'title', 'location', 'date', 'start_time', 'end_time', 'url'])
                ->groupBy(function($item) {
                    return $item->date;
                });
    }

    public function findEventById( $id )
    {
        return Event::find( $id );
    }
    
    public function createEvent(Array $data)
    {
        $event = new Event;
        
        $event->location = $data['location'];
        $event->title = $data['title'];
        $event->type = $data['type'];
        $event->date = $data['date'];
        $event->start_time = $data['start_time'];
        $event->end_time = $data['end_time'];
        $event->end = $data['end'];
        $event->start = $data['start'];
        $event->url = $data['url']??null;

    	if($event->save())
    	{
    		return $event;
    	}
    	return null;
    }

    public function updateEvent( Array $data, $event )
    {
        if( isset($data['title']) ) $event->title = $data['title'];
        if( isset($data['type']) ) $event->type = $data['type'];
        if( isset($data['date']) ) $event->date = $data['date'];
        if( isset($data['start_time']) ) $event->start_time = $data['start_time'];
        if( isset($data['end_time']) ) $event->end_time = $data['end_time'];
        if( isset($data['url']) ) $event->url = $data['url'];
        if( isset($data['location']) ) $event->location = $data['location'];

        if( $event->update() )
        {
            return $event;
        }

        return null;
    }

    public function bookEvent( $event, $worker )
    {
        $booking = new WorkerEventBooking;

        $booking->worker_id = $worker->id;
        $booking->event_id = $event->id;
    
        if( $booking->save() )
        {
            return $booking;
        }

        return null;
    }

}