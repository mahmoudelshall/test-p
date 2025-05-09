<?php

namespace App\Jobs;

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $email;
    protected string $code;

    /**
     * Create a new job instance.
     */
    public function __construct($name , $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->code = rand(100000, 999999);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update or create OTP
            Otp::updateOrCreate(
            ['email' => $this->email],
            ['code' => $this->code, 'updated_at' => Carbon::now()]
        );
        // Send Email
        try {
            SendEmailJob::dispatch($this->email, 'mails.verification-code', null, 'Yalla Host', ['name'=> $this->name, 'code'=>$this->code]);
        } catch (\Exception $e) {
            Log::error("Email sending failed: {$e->getMessage()}");
        }

    }
}
