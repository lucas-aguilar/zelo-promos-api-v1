<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Promotion;
use App\Promo_sale;
use App\Location;
use App\Lead;
use App\Leads_tag;
use App\Tag;
use \DrewM\MailChimp\MailChimp;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class LandingPageController extends BaseController
{
    public function index($id_oferta, $link_alias){
    	$promotion = Promotion::where('id', $id_oferta)->first();
    	$location = Location::where('id', $promotion->location_id)->first();
    	return view('lp', [
    		'location' => $location,
    		'lp_title' => $location->name,
    		'promotion' => $promotion,
    		'image_link' => ''
    	]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $promotion = Promotion::find($input['pid']);
        $location = Location::find($promotion->location_id);
        $user = User::where('id', $location->user_creator_id)->first();

        $MailChimp = new MailChimp($user->mch_api_key);

        $validator = Validator::make($input, [
            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'document' => 'required',
            'pid' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Por favor, confira se todos os campos foram preenchidos.', $validator->errors());
        }

        $email = strval($input['email']);
        $leads_list = Lead::where('email', $email)->get();
        $leads_qty = count($leads_list);

        if($leads_qty > 0){
            $leads_tags = Leads_tag::where('lead_id', strval($leads_list[0]->id))->where('location_id', $promotion->location_id)->get();
            $leads_tags_qty = count($leads_tags);
            if($leads_tags_qty > 0){
                if($promotion->new_leads_exclusive > 0){
                    return $this->sendError('Desculpe. Essa oferta é exclusiva para novos clientes.', []);
                }else{
                    for ($i=0; $i < $leads_tags_qty; $i++) { 
                        if($leads_tags[$i]->tag_id == $promotion->zelo_tag_reserved_id || $leads_tags[$i]->tag_id == $promotion->zelo_tag_sold_id){
                            return $this->sendError('Desculpe. Essa oferta já foi reservada ou utilizada por você.', []);
                        }
                    }
                }
            }
        }

        $tags = array($promotion->mch_tag_reserved_name, $location->mch_location_tag_name);

        $result = $MailChimp->post('lists/' . $user->mch_list_id . '/members', [
            'email_address' => $input['email'],
            'email_type' => 'html',
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $input['firstname'],
                'LNAME' => $input['lastname'],
                'PHONE' => $input['phone'],
                'DOCUMENT' => $input['document']
            ],
            'tags' => $tags
        ]);


        $return['mch_add_lead_result'] = $result;

        if($result['status'] == 400 && $result['title'] == 'Member Exists'){
            if($leads_qty > 0){
                $lead = $leads_list[0];
                $add_tags = [
                    0 => [
                        'name' => $promotion->mch_tag_reserved_name,
                        'status' => 'active'
                    ],
                    1 => [
                        'name' => $location->mch_location_tag_name,
                        'status' => 'active'
                    ]
                ];
                $tags_result = $MailChimp->post('lists/' . $user->mch_list_id . '/members/' . $leads_list[0]->mch_lead_id . '/tags', [
                    'tags' => $add_tags
                ]);
                $return['mch_add_lead_tags_result'] = $tags_result;
            }else{
                return $this->sendError('LEAD existe no MailChimp mas não existe na Zelo API. Por favor contate o suporte para sincronizar as bases de dados. ', []);
            }
        }else{
            $lead = new Lead;
            $lead->firstname = $input['firstname'];
            $lead->lastname = $input['lastname'];
            $lead->birthdate = $input['birthdate'];
            $lead->phone = $input['phone'];
            $lead->email = $input['email'];
            $lead->document = $input['document'];
            $lead->mch_lead_id = $result['id'];
            $lead->save();

            $return['lead'] = $lead;
        }

        $leads_tag = new Leads_tag;
        $leads_tag->tag_id = $promotion->zelo_tag_reserved_id;
        $leads_tag->lead_id = $lead->id;
        $leads_tag->location_id = $location->id;
        $leads_tag->save();
        $return['leads_tag_reserved'] = $leads_tag;

        $leads_tag = new Leads_tag;
        $leads_tag->tag_id = $location->zelo_location_tag_id;
        $leads_tag->lead_id = $lead->id;
        $leads_tag->location_id = $location->id;
        $leads_tag->save();
        $return['leads_tag_location'] = $leads_tag;

        $promo_sale = new Promo_sale;
        $promo_sale->promotion_id = $promotion->id;
        $promo_sale->location_id = $location->id;
        $promo_sale->mailchimp_lead_id = $lead->mch_lead_id;
        $promo_sale->lead_email = $lead->email;
        $promo_sale->lead_name = $lead->firstname;
        $promo_sale->lead_lastname = $lead->lastname;
        $promo_sale->lead_phone = $lead->phone;
        $promo_sale->lead_document = $lead->document;
        $promo_sale->total_sale_with_discount = 0;
        $promo_sale->lead_id = $lead->id;
        $promo_sale->sold = 0;
        $promo_sale->save();
        $return['promo_sale'] = $promo_sale;

        if((time()-(60*60*24)) <= strtotime($promotion->end_datetime)){
            $return['is_expired'] = 0;
            $result = $MailChimp->post($promotion->mch_pre_sale_automation_id, [
                'email_address' => $lead->email
            ]);
        }else{
            $return['is_expired'] = 1;
            $result = $MailChimp->post($promotion->mch_expired_welcome_automation_id, [
                'email_address' => $lead->email
            ]);
        }

        return view('thanks', [
    		'location_name' => $location->name,
    		'image_link' => ''
    	]);

    }
}
