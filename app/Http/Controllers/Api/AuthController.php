<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Rules\EmailMustHaveTLD;
use Auth;
use DB;

class AuthController extends Controller
{



    public function register(Request $request)
    {


     $validateData =  Validator::make($request->all(), [
            'name'=>'required|max:100',
            'email'=>['required','unique:users','email',new EmailMustHaveTLD], 
            'password'=>'required',
            'phone'=>'',
                ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
       }

        $validate = $request->validate([
            'name'=>'required|max:100',
            'email'=>'email|required|unique:users',
            'password'=>'required',
            'phone'=>'',
        ]);

        $validate['password'] = Hash::make($request->password);




        $user = User::create($validate);

        //$user->notify(new SignupActivate($user));
        $user->sendApiEmailVerificationNotification();

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user'=>$user,'access_token'=>$accessToken]);
    }




    public function login(Request $request)
    {


        $validateData =  Validator::make($request->all(), [
            'email'=>'email|required',
            'password'=>'required',
        ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
       }

      $loginData = $request->validate([
            'email'=>'email|required',
            'password'=>'required'
      ]);




      if(!auth()->attempt($loginData) ){
            return response(['error'=>'Invalid Credentials']);
      }
      $user = auth()->user();
    //   if(!auth()->check() || !auth()->user()->email_verified_at)
    //   {
    //     return response(['error'=>'Please activate account']);
    //   }


   

        
        

      // if(!$this->setUserDevices(auth()->user()->id,$request->player_id,$accessToken))
      // {
      //      return response(['error'=>'Error setting device']);
      // }

      return response(['user'=>$user]);
    }
    


    public function logout(Request $request)
    {

        $validateData =  Validator::make($request->all(), [
            'player_id'=>'required'
                ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
         }

        $device = Device::where('player_id',$request->player_id)->update(['active'=>'0']);
        $device = Device::where('player_id',$request->player_id)->first();
        $user_id = DB::table('oauth_access_tokens')->where('id', $device->oauth_id)->update(['revoked'=>'1']);


      return response(['result'=>$user_id]);

    }

    public function getTags(Request $request)
    {

        $validateData =  Validator::make($request->all(), [
            'id'=>'integer|required'
                ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
         }

         $tags = Tag::all();
         $userTags = UsersTag::where('user_id',$request->id)->get();
         $userTagArray = array();
       foreach ($userTags as $key=>$userTag)
       {    $tag = Tag::where('id',$userTag->tag_id)->first();
            $userTagArray[] = $tag;
       }

      return response(['tags'=>$tags,'usertags'=>$userTagArray]);

    }

    public function editUserTags(Request $request)
    {

        $validateData =  Validator::make($request->all(), [
            'id'=>'integer|required'
                ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
         }


        $userSelectedTags = json_decode($request->tags,true);
        $userTag = true;

       // UsersTag::where('user_id',$request->id)->delete();
          $date = date('Y-m-d H:i:s');



        foreach ($userSelectedTags as $key=>$userSelectedTag)
        {

                if (Tag::where('id', '=', $userSelectedTag)->exists()) {
                    if(!UsersTag::where('user_id',$request->id)->where('tag_id',$userSelectedTag)->exists())
                    {
                         $userTag = UsersTag::insert(['user_id'=>$request->id,'tag_id'=>$userSelectedTag,'created_at'=>$date,'type'=>'manual','views'=>'0']);
                        // $userTag = UsersTag::where('user_id',$request->id)->where('tag_id',$userSelectedTag)->delete();
                    }
                }

        }

         UsersTag::whereNotIn('tag_id', $userSelectedTags)->where('user_id',$request->id)->delete();


      return response(['result'=>$userTag]);

    }


    
    function editProfile(Request $request)
    {
        
             $validateData =  Validator::make($request->all(), [
                   'id'=>'integer|required'
                ]);

                if ($validateData->fails()) {
                    return response(['error'=>$validateData->messages()->first()]);
               }
 

            $user = '';
            if ($request->hasFile('photo')) {
                $image      = $request->file('photo');
                $fileName   = time() . '.' . $image->getClientOriginalExtension();
                Storage::disk('local')->put('public/users/Api/', $image, 'public');
                $user = User::where('id',$request->id)->update(['avatar' => 'users/Api/'.$image->hashName()]);

            }
         //    return response(['result'=>$request->hasFile('photo')]); 
            
            if ($request->name != null)
            {
                 $user = User::where('id',$request->id)->update(['name' => $request->name]);
            }
            if ($request->email != null)
            {
                 $user = User::where('id',$request->id)->update(['email' => $request->email]);
            }
            if ($request->phone != null)
            {
                 $user = User::where('id',$request->id)->update(['phone' => $request->phone]);
            }
            $user = User::where('id',$request->id)->first();
            
                            
                            
             $user['avatar'] = asset('storage/').'/'.$user->avatar;
                 $accessToken = $user->createToken('authToken')->accessToken;
            
            return response(['user'=>$user,'access_token'=>$accessToken]);
       
    }
    

    
    
     public function getUserDetails(Request $request)
    {


        $validateData =  Validator::make($request->all(), [
            'id'=>'integer|required'
                ]);

        if ($validateData->fails()) {
            return response(['error'=>$validateData->messages()->first()]);
       }
      $user = User::where('id',$request->id)->first(); 
      return response(['user'=>$user]);

    }
    
   public function sendMessage() {
    $content      = array(
        "en" => 'test michael'
    );

    $fields = array(
        'app_id' => "ec690d3b-b302-4b9a-b467-aa6e6ba8bdd2",
        // 'include_external_user_ids' => array(
        //     '35'
        // ),
        'include_player_ids' => array(
            '6c961b1e-27da-4589-a304-2950e314fc6c'
            ),
        'data' => array(
            "foo" => "bar"
        ),
        'contents' => $content,
    );

    $fields = json_encode($fields);
    print("\nJSON sent:\n");
    print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic YzdkZjIzZmItMmJiYi00ZDhlLWIzM2UtNjcyZTViZjYxNTMx'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


}
