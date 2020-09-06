<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function jsonResponse($status, $data = null, $message = null, $code)
{
    $resp_arr = array(
        'status' => $status,
        'data' => $data,
        'message' => $message,
        'code' => $code
    );
    return response()->json($resp_arr, $code);
}
