<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ReCaptchaV3Rule;
use App\Services\UserRegistrationService;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Apply guest middleware and inject user registration services.
     *
     * @return void
     */
    public function __construct(private readonly UserRegistrationService $registrations)
    {
        $this->middleware('guest');
    }

    /**
     * Build the validator used for public user registration input.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'agreement' => ['required'],
            'g-recaptcha-response' => [(config('settings.captcha_registration') ? 'required' : 'sometimes'), new ReCaptchaV3Rule('register')]
        ]);
    }

    /**
     * Create a public user account after registration validation passes.
     *
     * @param  array  $data
     * @return User|null
     */
    protected function create(array $data): ?User
    {
        return $this->registrations->createPublicUser($data);
    }

    /**
     * Display the registration form when public registration is enabled.
     *
     * @return View
     */
    public function showRegistrationForm(): View
    {
        // If registration is enabled
        if (config('settings.registration_registration')) {
            return view('auth.register');
        }

        abort(404);
    }
}
