<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Promotion;
use App\Promo_sale;
use App\Location;
use App\Lead;
use App\Tag;
use Validator;
use \DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Storage;

class PromotionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();

        if($user->permission_group < 3){
            $locations = Location::where('user_creator_id', $user->id)->get();
            $location_ids = array();

            foreach ($locations as $location) {
                $location_ids[] = $location->id;
            }
            $location_ids[] = $user->location_id;

            $promotions = Promotion::where('deleted', 0)->whereIn('location_id', $location_ids)->get();

            return $this->sendResponse($promotions->toArray(), 'Promotions retrieved successfully.');
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }

    /**
     * Set Promo_sale SOLD.
     *
     * @return \Illuminate\Http\Response
     */
    public function setPromoSaleSold(Request $request)
    {
        $user = auth('api')->user();
        if($user->permission_group == 3){
            
// -----------------

            // $location = Location::where('id', $user->location_id)->first();
            // $adminUser = User::where('id', $location-> )
            // $MailChimp = new MailChimp($adminUser->mch_api_key);

// -----------------
            $input = $request->all();
            $promo_sale_id = $input['promo_sale_id'];

            $promo_sale = Promo_sale::where('id', $promo_sale_id)->first();
            $lead = Lead::where('id', $promo_sale->lead_id)->first();
            $promotion = Promotion::where('id', $promo_sale->promotion_id)->first();

            if($user->location_id == $promotion->location_id){

                $promo_sale->sold = 1;
                $promo_sale->save();

                $add_tags = [
                    0 => [
                        'name' => $promotion->mch_tag_sold_name,
                        'status' => 'active'
                    ]
                ];
                $tags_result = $MailChimp->post('lists/' . $user->mch_list_id . '/members/' . $lead->mch_lead_id . '/tags', [
                    'tags' => $add_tags
                ]);

                $mail_result = $MailChimp->post($promotion->mch_after_sale_automation_id, [
                    'email_address' => $lead->email
                ]);

                $return['mch_after_sale_mail_result'] = $mail_result;
                $return['mch_tags_result'] = $tags_result;
                $return['promo_sale'] = $promo_sale;
                $return['lead'] = $lead;


                return $this->sendResponse($return, 'Venda de campanha registrada com sucesso');
            }
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }

    /**
     * Display a listing of the Promo_sales.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromoSales()
    {
        $user = auth('api')->user();
        $promo_sales = Promo_sale::where('location_id', $user->location_id)->where('sold', 0)->get();

        return $this->sendResponse($promo_sales->toArray(), 'Promoion sales retrieved successfully.');
    }

    /**
     * Display a listing of the SOLD Promo_sales.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSoldPromoSales()
    {
        $user = auth('api')->user();
        $promo_sales = Promo_sale::where('location_id', $user->location_id)->where('sold', 1)->get();

        return $this->sendResponse($promo_sales->toArray(), 'Promoion sales retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        if($user->permission_group == 2 || $user->permission_group == 1){
            $MailChimp = new MailChimp($user->mch_api_key);

            $input = $request->all();

            $validator = Validator::make($input, [
                'title' => 'required',
                'rules' => 'required',
                'internal_title' => 'required',
                'new_leads_exclusive' => 'required',
                'lp_link_alias' => 'required',
                'location_id' => 'required',
                'mch_pre_sale_automation_id' => 'required',
                'mch_after_sale_automation_id' => 'required',
                'mch_expired_welcome_automation_id' => 'required',
                'promo_image' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            if($validator->fails()){
                return $this->sendError('Alguns dados estão faltando.', $validator->errors());
            }

            $qtyPromoLinkAlias = Promotion::where('lp_link_alias', $input['lp_link_alias'])->count();

            if($qtyPromoLinkAlias > 0){
                return $this->sendError('Já existe uma campanha com esse link.');
            }

            $location = Location::find($input['location_id']);
            $location_tag_name = str_replace(' ', '', $location->name);

            $tag_name = str_replace(' ', '', $input['internal_title']);
            $tag_name = $location->id . '-' . $tag_name;


            $result = $MailChimp->post('lists/' . $user->mch_list_id . '/segments', [
                'name' => $tag_name . '-RESERVED',
                'static_segment' => []
            ]);

            $tag = new Tag;
            $tag->title = $tag_name . '-RESERVED';
            $tag->location_id = $input['location_id'];
            $tag->mch_id = $result['id'];
            $tag->save();

            $mch_tag_reserved_id = $tag->mch_id;
            $mch_tag_reserved_name = $tag->title;
            $zelo_tag_reserved_id = $tag->id;


            $result = $MailChimp->post('lists/' . $user->mch_list_id . '/segments', [
                'name' => $tag_name . '-SOLD',
                'static_segment' => []
            ]);

            $tag = new Tag;
            $tag->title = $tag_name . '-SOLD';
            $tag->location_id = $input['location_id'];
            $tag->mch_id = $result['id'];
            $tag->save();

            $mch_tag_sold_id = $tag->mch_id;
            $mch_tag_sold_name = $tag->title;
            $zelo_tag_sold_id = $tag->id;

            $promotion = new Promotion;
            $promotion->title = $input['title'];
            $promotion->description = $input['description'];
            $promotion->rules = $input['rules'];
            $promotion->unit_cost = $input['unit_cost'];
            $promotion->mailchimp_campaign_id = '';
            $promotion->location_id = $input['location_id'];
            $promotion->internal_title = $input['internal_title'];
            $promotion->end_datetime = $input['end_datetime'];
            $promotion->enabled = 0;
            $promotion->deleted = 0;
            $promotion->new_leads_exclusive = $input['new_leads_exclusive'];
            $promotion->qty_limit = $input['qty_limit'];
            $promotion->lp_link_alias = $input['lp_link_alias'];
            $promotion->mch_tag_reserved_id = $mch_tag_reserved_id;
            $promotion->mch_tag_reserved_name = $mch_tag_reserved_name;
            $promotion->zelo_tag_reserved_id = $zelo_tag_reserved_id;
            $promotion->mch_tag_sold_id = $mch_tag_sold_id;
            $promotion->mch_tag_sold_name = $mch_tag_sold_name;
            $promotion->zelo_tag_sold_id = $zelo_tag_sold_id;
            $promotion->mch_pre_sale_automation_id = $input['mch_pre_sale_automation_id'];
            $promotion->mch_after_sale_automation_id = $input['mch_after_sale_automation_id'];
            $promotion->mch_expired_welcome_automation_id = $input['mch_expired_welcome_automation_id'];

            $promotion->save();

            if($request->hasFile('promo_image'))
            {
                $mime = $request->file('promo_image')->getClientOriginalExtension();
                $filename = $input['location_id'] .'-'. $promotion->id . '.' . $mime;
                $request->file('promo_image')->storeAs('public/promo-images/' . $input['location_id'], $filename);
                $promotion->image_hash_name = $filename;
            }

            $promotion->save();

            $return['promotion'] = $promotion->toArray();
            $return['landing-page-link'] = 'oferta/' . $promotion->id . '/'/* . $input['lp_link_alias']*/;

            return $this->sendResponse($return, 'Promotion created successfully.');
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth('api')->user();
        $locations = Location::where('user_creator_id', $user->id)->get();
        $location_ids = array();
        foreach ($locations as $location) {
            $location_ids[] = $location->id;
        }
        $location_ids[] = $user->location_id;
        $promotion = Promotion::where('id', $id)->whereIn('location_id', $location_ids)->first();

        if (is_null($promotion)) {
            return $this->sendError('Promotion not found.');
        }

        return $this->sendResponse($promotion->toArray(), 'Promotion retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Promotion $promotion)
    {
        $user = auth('api')->user();
        $locations = Location::where('user_creator_id', $user->id)->get();
        $location_ids = array();
        foreach ($locations as $location) {
            $location_ids[] = $location->id;
        }
        $location_ids[] = $user->location_id;

        $promo_count = Promotion::where('id', $promotion->id)->whereIn('location_id', $location_ids)->count();
        if($user->permission_group == 2 || $user->permission_group == 1
        && $promotion->location_id == $user->location_id
        && $promo_count > 0){

            $input = $request->all();

            $validator = Validator::make($input, [
                'title' => 'required',
                'rules' => 'required',
                'internal_title' => 'required',
                'new_leads_exclusive' => 'required',
                'lp_link_alias' => 'required',
                'location_id' => 'required',
                'promo_image' => 'image|mimes:jpeg,png,jpg|max:4096'
            ]);


            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $promotion->title = $input['title'];
            $promotion->description = $input['description'];
            $promotion->rules = $input['rules'];
            $promotion->unit_cost = $input['unit_cost'];
            $promotion->mailchimp_campaign_id = '';
            $promotion->location_id = $input['location_id'];
            $promotion->internal_title = $input['internal_title'];
            $promotion->end_datetime = $input['end_datetime'];
            $promotion->new_leads_exclusive = $input['new_leads_exclusive'];
            $promotion->qty_limit = $input['qty_limit'];
            $promotion->lp_link_alias = $input['lp_link_alias'];
            $promotion->mch_pre_sale_automation_id = $input['mch_pre_sale_automation_id'];
            $promotion->mch_after_sale_automation_id = $input['mch_after_sale_automation_id'];

            if($request->hasFile('promo_image'))
            {
                $mime = $request->file('promo_image')->getClientOriginalExtension();
                $filename = $input['location_id'] .'-'.$tag_name . '.' . $mime;
                $request->file('promo_image')->storeAs('public/promo-images/' . $input['location_id'], $filename);
                $promotion->image_hash_name = Storage::url('public/promo-images/' . $input['location_id'] . '/' . $filename);
            }

            $promotion->save();


            $return['promotion'] = $promotion->toArray();
            $return['landing-page-link'] = '/' . $input['location_id'] . '/' . $input['lp_link_alias'];

            return $this->sendResponse($return, 'Promotion updated successfully.');
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promotion $promotion)
    {
        $user = auth('api')->user();
        $locations = Location::where('user_creator_id', $user->id)->get();
        $location_ids = array();
        foreach ($locations as $location) {
            $location_ids[] = $location->id;
        }
        $location_ids[] = $user->location_id;

        $promo = Promotion::where('id', $promotion->id)->whereIn('location_id', $location_ids)->first();
        if($promo){
            $promo->enabled = 0;
            $promo->save();

            return $this->sendResponse($promo->toArray(), 'Campanha desativada com sucesso.');
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enable($promotion_id)
    {
        $user = auth('api')->user();
        $locations = Location::where('user_creator_id', $user->id)->get();
        $location_ids = array();
        foreach ($locations as $location) {
            $location_ids[] = $location->id;
        }
        $location_ids[] = $user->location_id;

        $promo = Promotion::where('id', $promotion_id)->whereIn('location_id', $location_ids)->first();
        if($promo){
            $promo->enabled = 1;
            $promo->save();

            return $this->sendResponse($promo->toArray(), 'Campanha ativada com sucesso.');
        }else{
            return $this->sendError('Você não possui permissão para essa ação.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($promotion_id)
    {
        $user = auth('api')->user();
        $locations = Location::where('user_creator_id', $user->id)->get();
        $location_ids = array();
        foreach ($locations as $location) {
            $location_ids[] = $location->id;
        }

        $promotion_count = Promotion::where('id', $promotion_id)->whereIn('location_id', $location_ids)->count();
        if($promotion_count){
            $promotion = Promotion::where('id', $promotion_id)->whereIn('location_id', $location_ids)->first();
            
            $promotion->enabled = 0;
            $promotion->deleted = 1;
            $promotion->save();

            return $this->sendResponse($promotion->toArray(), 'Campanha excluída com sucesso.');
        }
        return $this->sendError('Você não possui permissão para essa ação.');
    }


}
