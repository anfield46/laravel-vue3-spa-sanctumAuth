<?php

namespace App\Helpers;
use Mail;

class SendEmail
{
  /**
   * send email to user
   * @param string $view blade view location
   * @param string $subject email subject
   * @param string $to email destination
   * @param array $data data for blade
   * @return array
   */
  public function call($view, $subject, $to, $data = null)
  {
    if (config('mail.USE_MAIL')) {
      try {
        // if (config('mail.MAIL_FOR_TESTING') <> null) {
        //   $to = config('mail.MAIL_FOR_TESTING');
        // }
        Mail::send($view, $data, function ($message) use ($to, $subject) {
          $message->from('no-reply@pupukkaltim.com', 'Report D1');
          $message->to('alamsyah1@pupukkaltim.com');
          $message->subject($subject);
        });

        return true;
      } catch (\Exception $e) {
        dd($e->getMessage());
        return false;
      }
    }else{
      // dd('else');
      return false;
    }
  }
}
