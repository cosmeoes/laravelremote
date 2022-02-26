<?php

namespace App\Http\Livewire;

use App\Notifications\FeedbackFormSubmitted;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class FeedbackForm extends Component
{
    public $name;
    public $email;
    public $message;
    public $success = '';

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'message' => 'required'
    ];

    public function submit()
    {
        $this->validate();

        Notification::route('slack', config('services.slack.hook'))
            ->notify(new FeedbackFormSubmitted($this->name, $this->email, $this->message));

        $this->name = "";
        $this->email = "";
        $this->message = "";
        session()->flash('message', "Awesome! Thanks for your feedback!");
    }

    public function render()
    {
        return view('livewire.feedback-form');
    }
}
