<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $to;
    protected $view;
    protected$text;
    protected $subject;
    protected $data;
    protected $attach;
    protected $attach_name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to = null, $view = null, $text = null, $subject = null, $data = [], $attach = null, $attach_name = null)
    {
        $this->to = $to;
        $this->view = $view;
        $this->text = $text;
        $this->subject = $subject;
        $this->data = $data;
        $this->attach = $attach;
        $this->attach_name = $attach_name;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_array($this->to) && !empty($this->to)) {
            foreach ($this->to as $to) {
                sendMail($to['email'], $this->view, $this->text, $this->subject, $this->data);
            }
        } else {
            sendMail($this->to, $this->view, $this->text, $this->subject, $this->data);
        }
    }
}
