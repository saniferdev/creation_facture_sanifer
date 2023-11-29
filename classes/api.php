<?php

Class API {

    public $link;
    public $url;
    public $key;


    public function getRest($url,$key,$num){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "Receipts":[
                {
                    "receipt_number" : [
                        {
                            "receipt_number":"'.$num.'"
                        }
                    ]
                }
            ]
          }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Token: '.$key
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }
    public function getData($url,$key,$num){
      $rest     = $this->getRest($url,$key,$num);
      $data     = json_decode($rest);
      $donne    = $data->response->data;
      $array    = array();
      foreach ($donne as $value) {
        $array[] = $value;
      }
      return $array;
    }

}
?>