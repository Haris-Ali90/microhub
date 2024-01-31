<?php

namespace App\Classes;

class MidMileClient {

    private $host = 'https://api.routific.com';
    private $resource = '/';
    private $version = 'v1';
    private $endPoint = 'vrp';
    private $endPoint2 = 'jobs';

    public function getJobId($result) {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.$this->resource.$this->version.$this->resource.$this->endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $result,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJfaWQiOiI1MzEzZDZiYTNiMDBkMzA4MDA2ZTliOGEiLCJpYXQiOjEzOTM4MDkwODJ9.PR5qTHsqPogeIIe0NyH2oheaGR-SJXDsxPTcUQNq90E'
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if (empty($error)) {
            $response = explode("\n", $response);
            $results = $response[count($response) - 1];
            $results = json_decode($results);
        } else {
            $results = [];
        }

        return $results;
    }
    /**
     * @param $id
     * @param $response
     * @param $shiftId
     * @param $orderId
     * @return string
     */
    public function getJobResponseByJobId($id)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host.$this->resource.$this->endPoint2.$this->resource.$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        return $result;




    }


//    public function getJobResults($id) {
//
//        $host = 'api.routific.com';
//
//        if (json_last_error() != JSON_ERROR_NONE) {
//            Log::write('error', 'Failed to JSON encode Routific request body');
//            throw new RoutingException();
//        }
//        $requestBody="";
//        $headers = [
//            'Accept-Encoding: utf-8',
//            'Accept: application/json; charset=UTF-8',
//            'Content-Type: application/json; charset=UTF-8',
//            'User-Agent: JoeyCo',
//            'Host:  api.routific.com',
//            'Authorization: bearer ' . 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI1Njk5ZDJjODUzNWFkMTBkMWQ0YmFlMTgiLCJpYXQiOjE2MDA0NDM0MTB9.ZS_LvnToeLObd3IdAuy5JEviQFjDHiEzaJac5P_w_b0',
//            'Content-Length: ' . strlen($requestBody)
//        ];
//        $ch = curl_init($this->host . $this->resource . $this->endPoint2 . $this->resource . $id);
//        curl_setopt($ch, CURLOPT_HEADER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//
//        $responseData = curl_exec($ch);
//
//        $error = curl_error($ch);
//        curl_close($ch);
//
//
//        if (empty($error)) {
//            $response = explode("\n", $responseData);
//            $httpCode = explode(' ', $response[0]);
//            $httpCode = $httpCode[1];
//
//            if ($httpCode >= 300) {
//                //   Log::write('error', print_r($responseData, true));
//                //throw new RoutingException();
//            }
//
//            $results = $response[count($response) - 1];
//            $results = json_decode($results, true);
//
//            if (json_last_error() != JSON_ERROR_NONE) {
//                //  Log::write('error', print_r($responseData, true));
//                //throw new RoutingException();
//            }
//        } else {
//            $results = [];
//        }
//
//        return $results;
//
//        dd($results);
//    }

}
