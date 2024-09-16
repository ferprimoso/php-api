<?php

namespace app\helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static $key; 
    private static $accessTokenExpiryTime = 3600; // Token válido por 1 hora
    private static $refreshTokenExpiryTime = 86400 * 30; // Refresh Token válido por 30 dias


    // SET JWT_SECRET
    public static function init()
    {
        self::$key = getenv('JWT_SECRET');
        if (self::$key === false) {
            throw new RuntimeException('JWT_SECRET environment variable not set.');
        }
    }

    public static function generateAccessToken($user)
    {
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
        try {
            return JWT::decode($token, new Key(self::$key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
