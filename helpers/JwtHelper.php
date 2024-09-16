<?php

namespace app\helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;

class JwtHelper
{
    private static $key;
    private static $accessTokenExpiryTime = 3600; // Token válido por 1 hora
    private static $refreshTokenExpiryTime = 86400 * 30; // Refresh Token válido por 30 dias

    public static function init()
    {
        self::$key = Yii::$app->params['jwtSecret'] ?? getenv('JWT_SECRET');
        if (!self::$key) {
            throw new \Exception('JWT secret key not found');
        }
    }

    public static function generateAccessToken($user)
    {
        self::init();

        $payload = [
            'iat' => time(),
            'exp' => time() + self::$accessTokenExpiryTime,
            'data' => [
                'user_id' => $user->id
            ]
        ];

        return JWT::encode($payload, self::$key, 'HS256');
    }

      // Gerar Refresh Token
      public static function generateRefreshToken($user)
      {
        self::init();

        $payload = [
            'iat' => time(),
            'exp' => time() + self::$refreshTokenExpiryTime,
            'data' => [
                'user_id' => $user->id
            ]
        ];

        return JWT::encode($payload, self::$key, 'HS256');
      }

    public static function validateToken($token)
    {
        self::init();

        try {
            return JWT::decode($token, new Key(self::$key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
