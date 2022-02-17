<?php


namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiClient;

class Mailchimp
{
    protected $client;

    public function __construct()
    {
        $this->client = new ApiClient();
        $this->client->setConfig([
            'apiKey' => config('services.mailchimp.api_key'),
            'server' => config('services.mailchimp.server_prefix'),
        ]);
    }

    public function addListMember($email, $time)
    {
        try {
            $this->client->lists->addListMember(config('services.mailchimp.list_id'), [
                'email_address' => $email,
                'status' => "subscribed",
                'merge_fields' => [
                    'TIME' => $time
                ],
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Error adding email to mailchimp $email $time : " . $e->getMessage() . ':' . $e->getTraceAsString());
        }

        return false;
    }

}
