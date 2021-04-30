<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use \DrewM\MailChimp\MailChimp;

class MailChimpController extends BaseController
{

    
    /**
     * Return 
     *
     * @return \Illuminate\Http\Response
     */
    public function getLists()
    {
		$MailChimp = new MailChimp(env('MAILCHIMP_API_KEY', null));

        $result = $MailChimp->get('lists');

        return $this->sendResponse($result, 'Mailchimp lists retrieved successfully.');
    }
    
    /**
     * Return 
     *
     * @return \Illuminate\Http\Response
     */
    public function getCampaigns()
    {
		$MailChimp = new MailChimp(env('MAILCHIMP_API_KEY', null));

        $result = $MailChimp->get('campaigns');

        return $this->sendResponse($result, 'Mailchimp campaigns retrieved successfully.');
    }
    
    /**
     * Return 
     *
     * @return \Illuminate\Http\Response
     */
    public function getAutomations()
    {
		$MailChimp = new MailChimp(env('MAILCHIMP_API_KEY', null));

        $result = $MailChimp->get('automations');

        return $this->sendResponse($result, 'Mailchimp automations retrieved successfully.');
    }
    
    /**
     * Return 
     *
     * @return \Illuminate\Http\Response
     */
    public function sendEmailTo(Request $request)
    {
		$MailChimp = new MailChimp(env('MAILCHIMP_API_KEY', null));

    	$input = $request->all();

		// https://us20.api.mailchimp.com/3.0/automations/5348333867/emails/17986c06d4/queue
        $result = $MailChimp->post('automations/5348333867/emails/17986c06d4/queue', [
        	'email_address' => $input['email']
        ]);

        return $this->sendResponse($result, 'Mailchimp automations retrieved successfully.');
    }
}