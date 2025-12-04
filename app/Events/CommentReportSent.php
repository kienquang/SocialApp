<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentReportSent implements ShouldBroadcast
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
    public $evidenceComment;
    public function __construct($reportId, $reporter, $reason, $evidenceComment)
    {
        $this->reportId = $reportId;
        $this->reporter = $reporter;
        $this->reason = $reason;
        $this->evidenceComment = $evidenceComment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('reports.comment');
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
            'evidence_comment' => [
                'id' => $this->evidenceComment->id,
                'content' => $this->evidenceComment->content,
                'created_at' => $this->evidenceComment->created_at,
                'updated_at' => $this->evidenceComment->updated_at,
                'parent_id' => $this->evidenceComment->parent_id,
            ],
        ];
    }
}
