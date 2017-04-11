<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Validator;
use App\Common;
use App\Mail\ForgotPasswordEmail;
use App\User;

class UserController extends Controller
{
    public    $response = [];
    protected $codeMessage;
    protected $user;
    protected $mail;

    const ACTIVE   = 1;
    const INACTIVE = 0;

    public function __construct(Common $codeMessage, User $user)
    {
        $this->codeMessage = $codeMessage;
        $this->user        = $user;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'name'     => 'alpha',
            'password' => 'required',
            'phone'    => 'numeric',
            'username' => 'required|min:5|max:35|unique:users,username',
        ]);

        if (!$validator->fails()) {
            if ($request->file) {
                $tt   = $request->file;
                $path = base_path().'/resources/profileImages';

                $binary = base64_decode($tt);
                $time   = $path.'/'.time().'.jpg';
                $file   = fopen($time, 'wb');
                fwrite($file, $binary);
                fclose($file);
                $this->user->profileImageURL = $time;
            }

            $this->user->username         = $request->username;
            $this->user->password         = Hash::make($request->password);
            $this->user->email            = $request->email;
            $this->user->name             = $request->name ? $request->name : null;
            $this->user->phone            = $request->phone ? $request->phone : null;
            $this->user->activation_token = str_random(60);

            $this->user->save();

            Mail::to($this->user)->send(new activationEmail($this->user));
            $response = [
                'code' => '0013',
            ];
        } else {
            $response = [
                'errors' => $validator->errors(),
                'code'   => '0014',
                'data'   => $request->all(),
            ];
        }

        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }


    public function login(Request $request)
    {
        $loginError = true;
        $field = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $usr   = $this->user->where($field, $request->identity)->first();

        if ($usr && ($usr->api_token == null)) {
            if (Hash::check($request->password, $usr->password)) {
                $loginError = false;
                if (!$usr->active) {
                    $response = [
                        'uses' => $field,
                        'user' => $usr,
                        'code' => '0031',
                    ];
                } else {
                    $usr->api_token    = str_random(60);
                    $usr->forgot_token = null;
                    $usr->save();
                    $response = [
                        'uses' => $field,
                        'user' => $usr,
                        'code' => '0011',
                    ];
                }
            }

        }
        if ($loginError) {
            $response = [
                'uses' => $field,
                'code' => '0012',
            ];
        }

        $response['message'] = sprintf($this->codeMessage->code($response['code']), $field);

        return response($response);

    }


    public function activate($token)
    {
        $usr = $this->user->whereActivationToken($token)->first();
        if (!isset($usr)) {
            $response['code'] = '0052';
        } else {
            $usr->active           = self::ACTIVE;
            $usr->activation_token = null;
            $usr->save();

            $response['code'] = '0015';
        }

        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    public function update(Request $request)
    {

    }

    public function mailForgotPassword(Request $request)
    {
        $usr = $this->user->whereEmail($request->email)->first();

        if ($usr) {
            $usr->forgot_token = str_random(60);
            $usr->save();
            Mail::to($usr)->send(new ForgotPasswordEmail($usr));
            $response['code'] = "0023";
        } else {
            $response['code'] = "0022";
        }

        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }


    public function tokenCheckForgotPassword($token = null)
    {
        $usr = $this->user->whereForgotToken($token)->first();
        if ($usr) {
            $error = '';
            $usr->forgot_token = null;
            $usr->save();
            $email = $usr->email;

            return view('forgotPasswordForm', compact('email', 'error'));
        } else
            $response['code'] = '0052';

        $response['message'] = $this->commonRequest->code($response['code']);

        return response($response);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newPassword' => 'required|confirmed',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors();
            $email = $request->email;

            return view('forgotPasswordForm', compact('email', 'error'));
        } else {
            $usr           = $this->user->whereEmail($request->email)->first();
            $usr->password = Hash::make($request->newPassword);
            $usr->save();

            $response['code'] = '0024';

            $response['message'] = $this->commonRequest->code($response['code']);
        }
        return response($response['message']);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_token'   => 'required',
            'oldPassword' => 'required',
            'newPassword' => 'required',
        ]);

        if ($validator->fails()) {
            $response            = [
                'status' => '0014',
                'errors' => $validator->errors(),
            ];
            $response['message'] = $this->codeMessage->code($response['code']);

            return response($response);
        }

        $usr = $this->user->whereApiToken($request->api_token)->first();
        if ($usr) {
            if (Hash::check($request->oldPassword, $usr->password)) {
                $usr->password = Hash::make($request->newPassword);
                $usr->save();
                $response ['code'] = '0001';
            } else
                $response ['code'] = '0021';
        } else
            $response = [
                'status' => '0032',
                ];
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

    public function logout(Request $request)
    {
        if ($user = User::whereApiToken($request->api_token)->first()) {
            $user->update(['api_token' => null]);
            $response['code'] = '0020';
        } else
            $response['code'] = '0052';
        $response['message'] = $this->codeMessage->code($response['code']);

        return response($response);
    }

}
