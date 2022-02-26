<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Exception\RequestException;
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

    public function sendEmail($html, $subject, $segmentId)
    {
        try {
            $response = $this->client->campaigns->create([
                'type' => "regular",
                'recipients' => [
                    'segment_opts' => [
                        'saved_segment_id' => $segmentId,
                    ],
                    'list_id' => config('services.mailchimp.list_id')
                ],
                'settings' => [
                    'subject_line' => $subject,
                    'preview_text' => "Take a look at the new jobs at Laravel Remote",
                    'from_name' => "Laravel Remote",
                    'reply_to' => "contact@laravelremote.com",
                    'inline_css' => true
                ]
            ]);


            $this->client->campaigns->setContent($response->id, [
                'html' => $html,
            ]);

            $this->client->campaigns->send($response->id);

            return true;
        } catch (RequestException $e) {
            Log::error("Error sending email though mailchimp: " . $e->getMessage() . ':' . $e->getTraceAsString());
        }

        return false;
    }

}
