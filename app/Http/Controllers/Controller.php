<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


	/**
     * Return a success JSON response.
     *
     * @param  string  $message
     * @param  int|null  $code
     * @param  array|string|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
	public function success(string $message = null, int $code = 200, $data = null)
	{
		return response()->json(
            [
                'status'    => 1,
                'message'   => $message,
                'data'      => $data
		    ],
            $code
        );
	}
    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
	public function error(string $message = null, int $code, $data = [])
	{
		return response()->json(
            [
                'status' => 0,
                'message' => $message,
                'data' => $data
		    ],
            $code
        );
	}
}
