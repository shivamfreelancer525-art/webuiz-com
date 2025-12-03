<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class FormController extends Controller
{
    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250',
            'subject' => 'required|string|max:250',
            'message' => 'required|string|max:500',
        ]);
        
        if ($validator->fails()) {

            return redirect()->back()->with('error', 'Something went wrong');
        }
    
        try {
         
            Mail::to('contact@app.webuiz.com')->send(new ContactFormMail($request->all()));
            return redirect()->back()->with('success', 'Your message has been sent successfully!');

        } catch (\Exception $e) {           

            return redirect()->back()->with('error', 'There was an error sending your message. Please try again later.');
        }
    }
}
