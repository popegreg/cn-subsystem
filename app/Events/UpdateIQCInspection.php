<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateIQCInspection extends Event
{
    use SerializesModels;
    public $con;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conn)
    {
        $this->con = $conn;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
