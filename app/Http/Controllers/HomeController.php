<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function home()
    {
          $userid = \Auth::user()->id;
          $get =  DB::table('userprofile')
                        ->join('profile_data','userprofile.profilepic', '=','profile_data.id')
                        ->join('users','userprofile.userid','=','users.id')
                        ->select(DB::raw('profile_data.profile_picture as profile')
                        ,'userprofile.age as age',
                        'userprofile.email as mail',
                        'userprofile.firstname as firstname',
                        'userprofile.lastname as lastname',
                        'users.name as username')
                        ->where('users.id',$userid)
                        ->get()->first();
        
        $splice = explode('@',$get->mail);
        $info = ['email'=>$splice[0],'info'=> $get,'id'=> $userid ];
        return response()->json($info);
                        
    }
    public function gallery(){
         $userid = \Auth::user()->id;
         $gallery = DB::table('profile_data')
         ->where('userid', $userid)->get();
         return response()->json($gallery);
    }
    public function chat(){
        $userid = \Auth::user()->id;
        $user = DB::table('userprofile')
        ->select(DB::raw('userprofile.userid as creatorid'),
        'profile_data.profile_picture as profilepicture',
        'userprofile.firstname as firstname',
        'userprofile.lastname as lastname',
        'users.name as username',
        'users.email as mail')
        ->join('users','userprofile.userid','=','users.id')
        ->join('profile_data', 'userprofile.profilepic', '=','profile_data.id')
        ->where('userprofile.userid','!=', $userid)
        ->get();
         //->where('userid', $userid)->get();
        return response()->json($user);
    }
    public function conversation(request $req){
   
           $userid = \Auth::user()->id;
           $getreceiver = DB::table('message_map')
          ->where('conversation_id',$req->token)
          ->where('userid_sender',$userid)
          ->pluck('userid_receiver')->first();
          $seen = DB::table('message')->where('conversation_id',$req->token)
          ->orderby('id','desc')
          ->pluck('seen')
          ->first();
          $chat = DB::table('message_map')

                ->join('message','message_map.conversation_id','=','message.conversation_id')
                ->join('users','message.sender_id','=','users.id')
                ->join('userprofile','message.sender_id','=','userprofile.userid')
                ->join('profile_data','userprofile.profilepic','=','profile_data.id')
                ->where('message.conversation_id', $req->token)
                ->where('message_map.userid_sender',$userid)
                ->orderBy('message.id','desc')
                ->paginate(15);
          $arr = ['data'=>$chat,
                  'receiver_id'=>$getreceiver,
                  'seen'=>$seen];       
         return response()->json($arr);
    }
    public function createconversation(request $req){
       $userlogin = \Auth::user()->id;
       $useroutside = $req->data['id'];
       $rotateid =  md5($userlogin.$useroutside);
       $conversationid = md5($userlogin.$useroutside);
        $sql = DB::table('message_map')->where('conversation_id', $conversationid)->pluck('conversation_id')->first();
       if($sql){
          if($sql == $conversationid){
            return response()->json(['token'=>$rotateid]);
          }
       }else{
          $rotateid =  md5($useroutside.$userlogin);
          $sqlcount = DB::table('message_map')->where('conversation_id', $rotateid)->get()->count();
          $sqlcheck = DB::table('message_map')->where('conversation_id', $rotateid)->pluck('conversation_id');
            if($sqlcheck){
                    
                 if($sqlcount == 0){
                     DB::table('message_map')
                     ->insert([
                         'userid_sender'=>$userlogin,
                         'userid_receiver'=>$req->data['id'],
                         'conversation_id'=>$rotateid
                     ]);
                     DB::table('message_map')
                     ->insert([
                         'userid_sender'=>$req->data['id'],
                         'userid_receiver'=>$userlogin,
                         'conversation_id'=>$rotateid
                     ]);
            
                     DB::table('message')
                     ->insert([
                         'conversation_id'=>$rotateid,
                         'sender_id'=>$userlogin,
                         'receiver_id'=>$req->data['id'],
                         'message_body'=>'Hi'
                     ]);
                }else{
                     return response()->json(['token'=>$rotateid]);
                }
                      
                  }else{
                    DB::table('message_map')
                    ->insert([
                        'userid_sender'=>$userlogin,
                        'userid_receiver'=>$req->data['id'],
                        'conversation_id'=>$conversationid
                    ]);
                    DB::table('message_map')
                    ->insert([
                        'userid_sender'=>$req->data['id'],
                        'userid_receiver'=>$userlogin,
                        'conversation_id'=>$conversationid
                    ]);
                    
               
            }
       }
  
     
       return response()->json(['token'=>$rotateid]);
    }
    public function conversationuser(){
        $userlogin = \Auth::user()->id;
        $s = DB::table('message_map')
            ->select(DB::raw('sender.name as sendername'),
            'receiver.name as receivername',
            'message_map.userid_sender as senderid',
            'message_map.userid_receiver as receiverid',
            'message_map.conversation_id as conversationid',
            'senderprofilepic.profile_picture as senderprofiledp',
            'receiverprofilepic.profile_picture as receiverprofiledp')
            ->join('users as sender','message_map.userid_sender','=','sender.id')
            ->join('users as receiver','message_map.userid_receiver','=','receiver.id')
            ->join('userprofile as senderprofile','message_map.userid_sender', '=','senderprofile.userid')
            ->join('userprofile as receiverprofile','message_map.userid_receiver', '=','receiverprofile.userid')
            ->join('profile_data as senderprofilepic','senderprofile.profilepic','=','senderprofilepic.id')
            ->join('profile_data as receiverprofilepic','receiverprofile.profilepic','=','receiverprofilepic.id')
            ->where('message_map.userid_sender',$userlogin)
            ->where('message_map.userid_receiver','!=',$userlogin)
         ->get();
        return response()->json($s);
        }
       
       
    public function chatfunction(request $req){

        function detectUTF8($string)
        {
            return preg_match('%(?:
                        [\xC2-\xDF][\x80-\xBF]         
                        |\xE0[\xA0-\xBF][\x80-\xBF]                
                        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}       
                        |\xED[\x80-\x9F][\x80-\xBF]             
                        |\xF0[\x90-\xBF][\x80-\xBF]{2}    
                        |[\xF1-\xF3][\x80-\xBF]{3}               
                        |\xF4[\x80-\x8F][\x80-\xBF]{2}    
                        )+%xs', $string);
            } 

            if(detectUTF8($req->data['message_body'])){  
                if(strpbrk($req->data['message_body'],"qwertyuiopasdfghjklzxcvbnm,./;'[]\=-0987654321`~<>?!@#$%^&*()_+{}|:") 
                  || strpbrk($req->data['message_body'],'"')){
                     $unicode = 0;
                }else{
                     $unicode = 1;
                }
            }else{
                     $unicode = 0;
            }
            

        date_default_timezone_set('Asia/Manila');
       $msg =   DB::table('message')->insertGetId([
            'conversation_id'=>$req->data['conversation_id'],
            'sender_id'=>$req->data['sender_id'],
            'receiver_id'=>$req->data['receiver_id'],
            'message_body'=>$req->data['message_body'],
            'seen'=>0,
            'unicode'=>$unicode,
            'created'=> Carbon::now(),

        ]);
        $userid = \Auth::user()->id;
        $getreceiver = DB::table('message_map')
       ->where('conversation_id',$req->data['conversation_id'])
       ->where('userid_sender',$userid)
       ->pluck('userid_receiver')->first();
       $chat = DB::table('message_map')
             ->join('message','message_map.conversation_id','=','message.conversation_id')
             ->join('users','message.sender_id','=','users.id')
             ->join('userprofile','message.sender_id','=','userprofile.userid')
             ->join('profile_data','userprofile.profilepic','=','profile_data.id')
             ->where('message.conversation_id', $req->data['conversation_id'])
             ->where('message_map.userid_sender',$userid)
             ->where('message.id', $msg)
             ->orderBy('message.id','desc')
             ->paginate(15);
       $arr = ['data'=>$chat,
               'receiver_id'=>$getreceiver];       
      return response()->json($arr);

    }
    public function updateseen(request $req){
   
   
     $check = DB::table('message')
            ->where('conversation_id',$req->data['tokenid'])
            ->orderby('id','desc')
            ->pluck('sender_id')
            ->first();
     if($req->data['senderid'] == $check){

     }else{
       $seenID = DB::table('message')
      ->where('conversation_id',$req->data['tokenid'])
      ->orderby('id','desc')
      ->update(['seen'=>1]);
      }
    
      $getID = DB::table('message')->where('conversation_id',$req->data['tokenid'])->OrderBy('id','desc')
      ->pluck('id')->first(); 
      $getData = DB::table('message')->where('id',$getID)->get();
 
      


        return response()->json($getData);

    }
    public function getSenderinfo (request $req){
        
        $userid = \Auth::user()->id;
        $getID = DB::table('message_map')->where('conversation_id',$req->token)
                             ->where('userid_sender', '=',$userid)->pluck('userid_receiver')->first();
        $get =  DB::table('userprofile')
        ->join('profile_data','userprofile.profilepic', '=','profile_data.id')
        ->join('users','userprofile.userid','=','users.id')
        ->select(DB::raw('profile_data.profile_picture as profile')
        ,'userprofile.age as age',
        'userprofile.email as mail',
        'userprofile.firstname as firstname',
        'userprofile.lastname as lastname',
        'users.name as username')
        ->where('users.id',$getID)
        ->get()->first();
        return response()->json($get);
    }
    public function notify(){
        $userid = \Auth::user()->id;
       return $count = DB::table('message')
       ->where('sender_id',$userid)
       ->where('seen', 0)
       ->where('receiver_id', 4)
       ->count();
    }
}
                                                                                 