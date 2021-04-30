<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Location;
use App\Tag;
use App\User;
use App\Promotion;
use Validator;
// use \DrewM\MailChimp\MailChimp;

class LocationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();
        if($user->permission_group == 2 && $user->enabled == 1){
            $locations = Location::where('user_creator_id', $user->id)->where('deleted', 0)->get();
            return $this->sendResponse($locations->toArray(), 'Lista de locais retornada com sucesso.');
        }else if($user->permission_group == 1 && $user->enabled == 1){
            $locations = Location::all()->get();
            return $this->sendResponse($locations->toArray(), 'Lista de locais retornada com sucesso.');
        }else{
            return $this->sendError('Você não possui permissão para essa ação.');
        }
    }

    public function getLoggedUser()
    {
        $user = auth('api')->user();
        return $this->sendResponse($user->toArray(), 'Dados do usuário logado retornados.');
    }

    /**
     * Update images in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_image(Request $request)
    {
        $user = auth('api')->user();
        if($user->permission_group == 2 || $user->permission_group == 1){
            $input = $request->all();

            $validator = Validator::make($input, [
                'location_id' => 'required',
                'type' => 'required|string|min:4|max:5',
                'image' => 'image|mimes:png|max:1024',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $location = Location::where('id', $input['location_id'])->where('user_creator_id', $user->id)->where('deleted', 0)->first();
            if(is_null($location)){
                return $this->sendError('Location not found.');
            }

            if($request->hasFile('image')){
                $mime = $request->file('image')->getClientOriginalExtension();
                $filename = $location->id . '.' . $mime;
                $request->file('image')->storeAs('public/' . $input['type'] . '-images/' . $location->id, $filename);
            }else{
                return $this->sendError('Image not found.');
            }

            return $this->sendResponse('Success', 'Imagem salva com sucesso.');
        }
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();
        if($user->permission_group == 2){
            // $MailChimp = new MailChimp($user->mch_api_key);

            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'string|min:3|max:100',
                'small_desc' => 'string|max:100',
                'address' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'link' => 'required',
                'logo_image' => 'image|mimes:png|max:1024',
                'cover_image' => 'image|mimes:png|max:1024',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $location_count = Location::where('link', $input['link'])->count();
            if($location_count > 0){
                return $this->sendError('Por favor escolha outro nome para esta empresa. Caso haja problema, entre em contato com o suporte!');
            }

            $location = new Location;
            $location->name = $input['name'];
            $location->address = $input['address'];
            $location->email = $input['email'];
            $location->phone = $input['phone'];
            $location->link = $input['link'];
            $location->description = $input['description'];
            $location->small_desc = $input['small_desc'];
            $location->operation_hours = $input['operation_hours'];
            $location->user_creator_id = $user->id;
            $location->zelo_location_tag_id = 0;
            $location->enabled = 1;
            $location->deleted = 0;
            $location->mch_location_tag_id = '';
            $location->mch_location_tag_name = '';
            $location->save();

            $tag_name = $location->id . str_replace(' ', '', $input['name']);

            // $result = $MailChimp->post('lists/' . $user->mch_list_id . '/segments', [
            //     'name' => $tag_name,
            //     'static_segment' => []
            // ]);

            // $return['mch_tag'] = $result;

            // if(is_array($result) && $result['id']){

                $tag = new Tag;
                $tag->title = $tag_name;
                $tag->location_id = $location->id;
                $tag->mch_id = '';//$result['id'];
                $tag->save();

                $location->mch_location_tag_id = $tag->id;//$result['id'];
                $location->mch_location_tag_name = $tag_name;//$tag_name;
                $location->save();

                $location->zelo_location_tag_id = $tag->id;
                $location->save();

                $return['location'] = $location;

                $return['tag'] = $tag;

                $user_password = rand(0, 99999999);

                $user = new User;
                $user->name = $location->name;
                $user->email = $location->email;
                $user->password = bcrypt($user_password);
                $user->permission_group = 3;
                $user->enabled = 1;
                $user->mch_api_key = '';
                $user->mch_list_id = '';
                $user->location_id = $location->id;
                $user->save();

                $return['user'] = $user;
                $return['user_password'] = $user_password;

                if($request->hasFile('logo_image'))
                {
                    $mime = $request->file('logo_image')->getClientOriginalExtension();
                    $filename = $tag_name . '.' . $mime;
                    $request->file('logo_image')->storeAs('public/logo-images/' . $location->id, $filename);
                }

                if($request->hasFile('cover_image'))
                {
                    $mime = $request->file('cover_image')->getClientOriginalExtension();
                    $filename = $tag_name . '.' . $mime;
                    $request->file('cover_image')->storeAs('public/cover-images/' . $location->id, $filename);
                }

            // }else{
            //     $location->delete();
            //     return $this->sendError('Erro na criação da TAG no MailChimp.');
            // }

            return $this->sendResponse($return, 'Empresa criada com sucesso.');
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
        $location = Location::where('user_creator_id', $user->id)->where('id', $id)->where('deleted', 0)->first();

        if (is_null($location)) {
            return $this->sendError('Location not found.');
        }

        return $this->sendResponse($location->toArray(), 'Empresa retornada com sucesso.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'string|min:3|max:100',
            'small_desc' => 'string|max:100',
            'address' => 'required',
            'phone' => 'required',
            'link' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $location->name = $input['name'];
        $location->address = $input['address'];
        $location->description = $input['description'];
        $location->small_desc = $input['small_desc'];
        $location->email = $input['email'];
        $location->phone = $input['phone'];
        $location->link = $input['link'];
        $location->operation_hours = $input['operation_hours'];
        $location->save();

        return $this->sendResponse($location->toArray(), 'Empresa atualizada com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $user = auth('api')->user();
        $location_count = Location::where('id', $location->id)->where('user_creator_id', $user->id)->count();
        if($location_count > 0){
            $promotions = Promotion::where('location_id', $location->id)->get();
            foreach ($promotions as $key => &$promotion) {
                $promotion->enabled = 0;
                $promotion->save();
            }

            $users = User::where('location_id', $location->id)->get();
            foreach ($users as $key => &$user) {
                $user->enabled = 0;
                $user->save();
            }
            
            $location->enabled = 0;
            $location->save();

            return $this->sendResponse($location->toArray(), 'Empresa desativada com sucesso.');

        }else{
            return $this->sendError('Empresa não existe.');     
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enable($location_id)
    {
        $user = auth('api')->user();
        $location_count = Location::where('id', $location_id)->where('user_creator_id', $user->id)->count();
        if($location_count > 0){
            $users = User::where('location_id', $location_id)->get();
            foreach ($users as $key => &$user) {
                $user->enabled = 1;
                $user->save();
            }
            
            $location = Location::where('id', $location_id)->first();
            $location->enabled = 1;
            $location->save();

            return $this->sendResponse($location->toArray(), 'Empresa excluída com sucesso.');

        }else{
            return $this->sendError('Empresa não existe.');     
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($location_id)
    {
        $user = auth('api')->user();
        $location_count = Location::where('id', $location_id)->where('user_creator_id', $user->id)->count();
        if($location_count > 0){
            $promotions = Promotion::where('location_id', $location_id)->get();
            foreach ($promotions as $key => &$promotion) {
                $promotion->enabled = 0;
                $promotion->deleted = 1;
                $promotion->save();
            }

            $users = User::where('location_id', $location_id)->get();
            foreach ($users as $key => &$user) {
                $user->enabled = 0;
                $user->save();
            }
            
            $location = Location::where('id', $location_id)->first();
            $location->enabled = 0;
            $location->deleted = 1;
            $location->save();

            $return['location'] = $location;
            $return['promotions'] = $promotions;

            return $this->sendResponse($return, 'Empresa deletada com sucesso.');
        }else{
            return $this->sendError('Empresa não existe.');     
        }
    }
}
