<?php

namespace App\Jobs;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResetPasswordCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected string $code;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->code = rand(100000, 999999);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update or create OTP
        Otp::updateOrCreate(
            ['email' => $this->user->email],
            ['code' => $this->code, 'updated_at' => Carbon::now()]
        );
        // Send Email
        try {
            SendEmailJob::dispatch($this->user->email, 'mails.reset-password', null, 'Yalla Host Reset Password', ['user' => $this->user, 'code' => $this->code]);
        } catch (\Exception $e) {
            Log::error("Email sending failed: {$e->getMessage()}");
        }
    }
}
