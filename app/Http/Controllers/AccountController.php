<?php

namespace App\Http\Controllers;

use App\DataModel\DBManager\Database;
use App\DataModel\Manager\SessionManager;
use App\DataModel\Manager\UserManager;
use App\DataModel\Model\JWT;
use App\DataModel\Model\LeaveArray;
use App\DataModel\Model\User;
use App\DataModel\Model\TaskArray;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function App\Helpers\globalResponse;

class AccountController extends Controller
{
    private int $successStatusCode  = 200;
    private int $unauthorizedAction = 401;
    private int $unprocessedEntity  = 442;
    private int $errorStatusCode    = 500;
    private int $notFound           = 404;

    public function postSignupAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data        = $request->all();
        $user        = new User();
        $userManager = new UserManager();

        $user->setUserName($data['username']);
        $user->setPassword($data['password']);
        $user->setStatus(1);
        if (!filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
            $user->setUserName(substr($data['username'],-10));
        }

        try {
            $rules = [
                'username' => 'required|unique:users',
                'password' =>
                    [
                        'required',
                        'min:8',
                        'regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,}$/'
                    ]
            ];
            $customMessages = [
                'unique'            => 'Oops! This email is already living its best life with someone else. Try another!',
                'username.required' => 'Hold on! A username is essential. Let’s not leave it blank like a mystery novel!',
                'password.required' => 'Even my grandma could guess that password. Use something tougher!',
                'password.min'      => "Nice try, but we need at least 8 characters. Don't be shy, add a few more!",
                'password.regex'    => 'Is this a password or a grocery list? Mix in some uppercase, lowercase, and numbers for flavor!'
            ];
            $validator = \Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                return globalResponse([], $validator->errors()->first(), false, $this->errorStatusCode);
            }

            $user = $userManager->createUser($user);
            $payload = [
                'iat'    => time(),
                'iss'    => 'localhost',
                'exp'    => time() + (TaskArray::$apiTimeToLive),
                'userId' => $user->getId(),
            ];

            $token = JWT::encode($payload, TaskArray::$apiSecretKey);
            $userManager->setCacheAppToken($user->getId(), $token);
            $data = [
                    'loggedIn'        => true,
                    'id'              => $user->getId(),
                    'name'            => $user->getName(),
                    'username'        => $user->getUserName(),
                    'status'          => $user->getStatus(),
                    'jwToken'         => $token
            ];
            (new SessionManager())->setSession('userInformation', $data);

            return globalResponse($data, 'You did it! The signup process couldn’t defeat you. Let the adventures begin!', true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function postLoginAction(Request $request)
    {
        $data        = $request->all();
        $user        = new User();
        $userManager = new UserManager();

        $user->setUserName($data['username']);
        $user->setPassword($data['password']);

        try {
            $userInfo = $userManager->getUserInfoByUsername($data['username']);
            if (count($userInfo)) {
                $userInfo = (array)$userInfo[0];
                $isPasswordCorrect = $user->getPassword() === $userInfo['password'];
                if ($isPasswordCorrect) {
                    $payload = [
                        'iat'    => time(),
                        'iss'    => 'localhost',
                        'exp'    => time() + (TaskArray::$apiTimeToLive),
                        'userId' => $userInfo['id'],
                    ];
                    $token = JWT::encode($payload, TaskArray::$apiSecretKey);
                    $userManager->setCacheAppToken($user->getId(), $token);

                    $data = [
                        'loggedIn'        => true,
                        'id'              => $userInfo['id'],
                        'name'            => $userInfo['name'],
                        'username'        => $user->getUserName(),
                        'status'          => $userInfo['status'],
                        'jwToken'         => $token
                    ];
                    (new SessionManager())->setSession('userInformation', $data);

                    return globalResponse($data, "Success! The system loves you and let you in!", true, $this->successStatusCode);
                }
                else{
                    return globalResponse([], 'Password incorrect. But don’t worry, we still like you!', false, $this->errorStatusCode);
                }
            }
            else{
                return globalResponse([], 'Are you sure you belong here? Maybe you’re in the wrong universe!', false, $this->errorStatusCode);
            }
        }
        catch (\Exception $ex){
            return globalResponse([], "Well, this is awkward! Something broke. Not your fault. Fault is here: ".$ex->getMessage(), false, $this->errorStatusCode);
        }
    }
}
