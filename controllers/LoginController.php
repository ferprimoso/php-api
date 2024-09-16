<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use app\helpers\JwtHelper;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class LoginController extends Controller
{
    public function actionLogin()
    {
        $request = Yii::$app->request->post();
        $username = $request['username'] ?? null;
        $password = $request['password'] ?? null;

        if (!$username || !$password) {
            throw new BadRequestHttpException('Username and password are required.');
        }

        $user = User::findByUsername($username);

        if (!$user) {
            throw new UnauthorizedHttpException('User not found.');
        }

        if (!$user->validatePassword($password)) {
            throw new UnauthorizedHttpException('Invalid password.');
        }

        $accessToken = JwtHelper::generateAccessToken($user);
        $refreshToken = JwtHelper::generateRefreshToken($user);

         // Store refresh token in the database
         $user->refresh_token = $refreshToken;
         if (!$user->save()) {
             throw new BadRequestHttpException('Failed to save refresh token.');
         }
 
         return [
             'access_token' => $accessToken,
             'refresh_token' => $refreshToken,
         ];
    }

    /**
     * Refreshes an access token using the refresh token.
     */
    public function actionRefreshToken()
    {
        $request = Yii::$app->request;
        $refreshToken = $request->post('refresh_token');

        if (!$refreshToken) {
            throw new BadRequestHttpException('Refresh token is required.');
        }

        // Find user by refresh token
        $user = User::findOne(['refresh_token' => $refreshToken]);
        if (!$user) {
            throw new UnauthorizedHttpException('Invalid refresh token.');
        }

        // Generate new access token (JWT)
        $accessToken = JwtHelper::generateAccessToken($user);

        return [
            'access_token' => $accessToken,
        ];
    }
}
