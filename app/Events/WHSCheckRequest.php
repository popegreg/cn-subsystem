<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WHSCheckRequest extends Event
{
    use SerializesModels;
    public $conn;
    public $transno;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conn,$transno)
    {
        $this->conn = $conn;
        $this->transno = $transno;
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
