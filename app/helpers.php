<?php

use Illuminate\Support\Facades\Log;

function encryptMessage($message)
{
    try {
        $key = base64_decode(env('AES_KEY'));
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($message, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    } catch (\Exception $e) {
        Log::error('Encryption failed: ' . $e->getMessage());
        return null;
    }
}

function decryptMessage($encrypted)
{
    try {
        $key = base64_decode(env('AES_KEY'));
        list($encrypted_data, $iv) = explode('::', base64_decode($encrypted), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    } catch (\Exception $e) {
        Log::error('Decryption failed: ' . $e->getMessage());
        return null;
    }
}

function errorResponse($data = null, $msg = 'Bad Request', $status_code = 400, $status = false)
    {
        $response = [
            'status' => $status,
            'code' => $status_code,
            'message' => $msg,
        ];

        if (is_array($data)) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }
        return response()->json($response, $status_code);
    }

function successResponse($data = null, $msg = 'OK', $status_code = 200, $status = true)
    {
        $response = [
            'status' => $status,
            'code' => $status_code,
            'message' => $msg,
        ];

        if (is_array($data)) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, 200);
    }
