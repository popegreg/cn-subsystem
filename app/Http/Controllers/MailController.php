<?php

namespace App\Http\Controllers;

class MailController extends Controller
{
	public function Sending_Email(Request $request_data)
	{
		var_dump($request_data);
	   //$this->call('GET','mail');
	   // return redirect('/pochangedelivery');
	   // return View('PoChangeDelivery');
	}
}