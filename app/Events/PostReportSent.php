<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostReportSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $reportId;
    public $reporter;
    public $reason;
    public $evidencePost;
    public function __construct($reportId, $reporter, $reason, $evidencePost)
    {
        $this->reportId = $reportId;
        $this->reporter = $reporter;
        $this->reason = $reason;
        $this->evidencePost = $evidencePost;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('reports.post');
    }

    public function broadcastWith()
    {
        return [
            'report_id' => $this->reportId,
            'reason' => $this->reason,
            'reported_at' => now(),
            'reporter' => [
                'id' => $this->reporter->id,
                'name' => $this->reporter->name,
                'email' => $this->reporter->email,
                'avatar' => $this->reporter->avatar,
                'cover_photo_url' => $this->reporter->cover_photo_url,
                'created_at' => $this->reporter->created_at,
            ],
            'evidence_post' => [
                'id' => $this->evidencePost->id,
                'title' => $this->evidencePost->title,
            ],
        ];
    }
}
