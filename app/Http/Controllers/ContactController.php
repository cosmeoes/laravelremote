<?php

namespace App\Http\Controllers;

use App\Notifications\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index() {
        return view('contact');
    }

    public function store(Request $request) {
        if ($request->input('website') === null) {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'message' => 'required',
            ]);

            Notification::route('slack', config('services.slack.hook'))
                ->notify(new ContactFormSubmitted($request->input('name'), $request->input('email'), $request->input("message")));
        }

        session()->flash('success', "Thank you! I'll contact you very soon.");
        return redirect()->back();
    }
}
