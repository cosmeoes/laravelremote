<?php

namespace App\Http\Controllers;

use App\Services\Mailchimp;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function store(Request $request, Mailchimp $mailchimp)
    {
        $validated = $request->validate([
            'time' => 'in:daily,weekly|required',
            'email' => 'email|required'
        ]);

        $mailchimp->addListMember($validated['email'], $validated['time']);

        session()->flash('success', 'You will receive jobs in your inbox :)');
        return redirect()->back();
    }
}
