<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        // kontakt formular
        return view('contact');
    }

    public function send(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'message' => 'required|string|min:10',
        ]);


        $toEmail = 'maxpc1685@gmail.com';

        // Простейшая отправка без отдельного email-шаблона
        Mail::raw(
            "New contact message from MaxPC:\n\n"
            . "Name: {$data['name']}\n"
            . "Email: {$data['email']}\n\n"
            . "Message:\n{$data['message']}",
            function ($message) use ($data, $toEmail) {
                $message->to($toEmail)
                    ->subject('New message from contact form')
                    // replyto nastavime na email od odosielatela
                    ->replyTo($data['email'], $data['name']);
            }
        );

        return back()->with('success', 'Your message has been sent. We will contact you soon.');
    }
}
