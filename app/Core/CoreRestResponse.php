<?php
namespace App\Core;

abstract class CoreRestResponse
{
    public static $STATUS_OK = 200;

    public static $STATUS_BAD_REQUEST = 400;
    public static $STATUS_UNAUTHORIZED = 401;
    public static $STATUS_FORBIDDEN = 403;
    public static $STATUS_NOT_FOUND = 404;

    public static $STATUS_SERVER_ERROR = 500;

    protected function encapsulateResponse($status, $data, $message = null)
    {
        return [
            'status' => $status,
            'message' => empty($message) ? $status : $message,
            'data' => $data,
        ];
    }

    public function responseDefault($data, $message = null)
    {
        try {
            return response()->json(
                $this->encapsulateResponse(
                    self::$STATUS_OK,
                    $data,
                    $message
                ),
                self::$STATUS_OK
            );
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) {
                $data = $e->getTrace();
                $message = $e->getMessage();
            } else {
                $data = 'EXCEPTION: INTERNAL SERVER ERROR';
                $message = 'INTERNAL SERVER ERROR';
            }

            return response()->json(
                $this->encapsulateResponse(
                    self::$STATUS_SERVER_ERROR,
                    $data,
                    $message
                ),
                self::$STATUS_SERVER_ERROR
            );
        }
    }

    abstract public function responseOk($data, $message = null);
    abstract public function responseServerError($data, $message = null);
}
