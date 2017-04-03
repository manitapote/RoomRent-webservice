<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller 
{

	public function store(Request $request)
	{
		$tt = $request->file;
		$path = base_path().'/resources/profileImages';
		// //echo $path;
		$binary = base64_decode($tt);
		$time = $path.'/'. time().'.jpg';
		$file = fopen($time, 'wb');
		fwrite($file, $binary);
		fclose($file);

		//echo $time;
		return response()->json(['image'=>$tt]);

	}
}