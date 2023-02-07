<?php

namespace App\Helpers;

class SendWa
{
  /**
   * send email to user
   * @param string $view blade view location
   * @param string $subject email subject
   * @param string $to email destination
   * @param array $data data for blade
   * @return array
   */
  public function d1_send_message($to, $message)
  {
    if (config('mail.USE_MAIL')) {
      try {
        $dataSending = array();
        $dataSending["api_key"] = "UVGO8N4D3JOI4XQZ";
        $dataSending["number_key"] = "UUWTknSqGzeBc0l5";
        $dataSending["phone_no"] = $to;
        $dataSending["message"] = $message;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($dataSending),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return true;
      } catch (\Exception $e) {
        // dd($e->getMessage());
        return false;
      }
    }else{
      // dd('else');
      return false;
    }
  }

  public function d1_send_file_url($to, $file)
  {
    if (config('mail.USE_MAIL')) {
      try {
        $dataSending = array();
        $dataSending["api_key"] = "UVGO8N4D3JOI4XQZ";
        $dataSending["number_key"] = "UUWTknSqGzeBc0l5";
        $dataSending["phone_no"] = $to;
        $dataSending["url"] = $file;
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.watzap.id/v1/send_file_url',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($dataSending),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return true;
      } catch (\Exception $e) {
        // dd($e->getMessage());
        return false;
      }
    }else{
      // dd('else');
      return false;
    }
  }
}
