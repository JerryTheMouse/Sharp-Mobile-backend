<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use Socialite;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */


    /**
     * Create a new authentication controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('api.guest', ['except' => 'logout']);
        $this->middleware('jwt.auth', ['only' => 'logout']);
    }


    public function logout(Request $request)
    {
        \JWTAuth::invalidate();
        return response()->json('You have logged out');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = \JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (\JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }


    /**
     * Retrieves  the user token from  the Google servers via code.
     *
     * @return Response
     */
    public function redirectToProvider(Request $request)
    {
        $code = $request->json()->get('code');
        $token = Socialite::driver('google')->getAccessToken($code);
        $socialite_user = Socialite::driver('google')->userFromToken($token);
        $google_id = $socialite_user->getId();
        if (!is_null($user = User::where('email', $socialite_user->getEmail())->first())) {
            $user->google_id = $google_id;
            $user->save();
            return $this->createJWTTokenFromUser($user);
        } else {
            $faker = \Faker\Factory::create();

            $data = [
                'fname' => explode(' ', $socialite_user->getName())[0],
                'lname' => explode(' ', $socialite_user->getName())[1],
                'email' => $socialite_user->getEmail(),
                'password' => bcrypt($faker->password),
                'google_id' => $google_id
            ];
            $user = $this->create($data);
            return $this->createJWTTokenFromUser($user);

        }

    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    protected function createJWTTokenFromUser($user)
    {
        $token = \JWTAuth::fromUser($user);
        return response()->json(compact('token'));

    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        $user = $this->create($request->all());
        return $this->createJWTTokenFromUser($user);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'google_id' => array_get($data,'google_id',null)
        ]);
    }
}
