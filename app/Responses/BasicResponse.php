<?php
namespace App\Responses;

use App\Core\CoreRestResponse;

class BasicResponse extends CoreRestResponse
{
    public function responseOk($data, $message = null)
    {
        return $this->responseDefault($data, $message);
    }

    public function responseServerError($data, $message = null)
    {
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
