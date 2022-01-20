<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;


/**
 * Class represent HTTP request for klaviyo
 */
class SendContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contact;
    public $authToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contact, $authToken)
    {
        $this->contact   = $contact;
        $this->authToken = $authToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            $data = [
                'token' => $this->authToken,
                'properties' => [
                    '$email'        => $this->contact->email,
                    '$first_name'   => $this->contact->name,
                    '$phone_number' => $this->contact->phone,
                    '$id'           => $this->contact->id
                ]
            ];

            $response = Http::asForm()->accept('text/html')->post(config('app.KLAVIYO_URL'), [
                'data' => json_encode($data)
            ]);

            if ($response->status() == 429) {
                $this->release(now()->addMinutes(60));
            }

        } catch (RateLimitException $exception) {
            $this->release(now()->addMinutes(60));
        } catch (Exception $e) {
            // like the connection failed or some other network error
            $this->failed($e);
        }
    }

    /**
     * Handle failed jod exceptions
     * Add exception log
     * 
     * @param $exception exceprion object
     * 
     * @return response string 
     */
    public function failed($exception)
    {
        Log::error($exception);
    }

    /**
     * Middleware function for rate limit 
     * For now 5 jobs per min failed if more than that.
     * 
     * @return obj of ratelimit
     */
    public function middleware()
    {
        return [new RateLimited('rateLimitKlaviyo')];
    }
}
