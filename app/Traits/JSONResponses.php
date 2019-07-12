<?php
namespace App\Traits;

trait JSONResponses
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

    /**
     * BASIC RESPONSE
     *
     * @param integer $status HTTP STATUS CODE
     * @param array $data response data
     *
     * @return json
     */
    protected function response($status, $data, $message = null)
    {
        try {
            return response()->json(
                $this->encapsulateResponse(
                    $status,
                    $data,
                    $message
                ),
                $status
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

    /**
     * SUCCESS RESPONSE (200)
     *
     * @param array $data response data
     * @param string $message (default:'Success!')
     *
     * @return json
     */
    protected function responseSuccess($data= [], $message= 'Success!')
    {
        $data['message'] = $message;
        return $this->response(static::$STATUS_OK, $data, $message);
    }

    /**
     * ERROR RESPONSE (500)
     *
     * @param array $data response data
     * @param string $message (default:'Error!')
     *
     * @return json
     */
    protected function responseError($data= [], $message= 'Error!')
    {
        $data['message'] = $message;
        return $this->response(static::$STATUS_SERVER_ERROR, $data, $message);
    }

    /**
     * UNAUTHORIZED RESPONSE (401)
     *
     * @param array $data response data
     * @param string $message (default:'Unauthorized!')
     *
     * @return json
     */
    protected function responseUnauthorized($data= [], $message= 'Unauthorized!')
    {
        $data['message'] = $message;
        return $this->response(static::$STATUS_UNAUTHORIZED, $data, $message);
    }

    /**
     * FORBIDDEN RESPONSE (403)
     *
     * @param array $data response data
     * @param string $message (default:'Forbidden!')
     *
     * @return json
     */
    protected function responseForbidden($data= [], $message= 'Forbidden!')
    {
        $data['message'] = $message;
        return $this->response(static::$STATUS_FORBIDDEN, $data, $message);
    }
}
