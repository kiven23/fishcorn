<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Auth;
use DB;
 
class ProfileController extends Controller
{
   public function uploadapi(Request $request){
    $userid = Auth::user()->id;
    $this->validate($request, [
          
      'file_path'=>'required'
  ]);
  try {
      $file_original_path = (object) ['file_path' => ""];        
      if ($request->hasFile('file_path')) {
          $original_filename = $request->file('file_path')->getClientOriginalName();
          $original_filename_arr = explode('.', $original_filename);
          $file_ext = end($original_filename_arr);
          $destination_path = './uploads/files/';
          $file_path_name = 'C-' . time() . '.' . $file_ext;

          if ($request->file('file_path')->move($destination_path, $file_path_name )) {
             
              $uploadPath = '/uploads/files/'.$file_path_name ;

                $id = DB::table('profile_data')->insertGetId([
                    'userid' => $userid,
                    'profile_picture' =>  $uploadPath,
                  ]);
                    DB::table('userprofile')
                    ->where('userid',$userid)
                    ->update([
                    'profilepic'=> $id]);

              return response() -> json(['path'=>$uploadPath, 'message' => 'File has been Successfully Uploaded'], 201);
          } else {
              return response() -> json('Cannot upload file');
          }}
      } catch (Exception $e) {
               throw new NotFoundException('Upload Failed');
      }
}
  public function createprofile(request $request){
    $userid = Auth::user()->id;
    $checkuser = DB::table('userprofile')->where('userid',$userid)->get();
    
      if(!count($checkuser) == 0){
          DB::table('userprofile')
          ->where('userid',$userid)
          ->update(
            ['email'=> $request->email,
            'lastname'=> $request->lastname,
            'firstname'=> $request->firstname,
            'age'=> $request->age,
            'userid'=> $userid]
          );
       }else{
          DB::table('userprofile')
          ->insert(
            ['email'=> $request->email,
            'lastname'=> $request->lastname,
            'firstname'=> $request->firstname,
            'age'=> $request->age,
            'userid'=> $userid]
          );
      }

 
       return response()->json('success');

  }
 

}
