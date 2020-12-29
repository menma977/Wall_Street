<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class AddUser extends Notification
{
  use Queueable;

  /**
   * The callback that should be used to create the verify email URL.
   *
   * @var Closure|null
   */
  public static $createUrlCallback;

  /**
   * The callback that should be used to build the mail message.
   *
   * @var Closure|null
   */
  public static $toMailCallback;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param mixed $notifiable
   * @return MailMessage
   */
  public function toMail($notifiable)
  {
    $verificationUrl = $this->verificationUrl($notifiable);

    return (new MailMessage)
      ->subject('Verify Email Address')
      ->line('Your Data')
      ->line('username : ' . $notifiable->username)
      ->line('password : ' . $notifiable->password_junk)
      ->line('secondary password : ' . $notifiable->secondary_password_junk)
      ->line('Your Wallet')
      ->line('Wallet Doge : ' . $notifiable->wallet_doge)
      ->line('Wallet ETH : ' . $notifiable->wallet_eth)
      ->line('Wallet LTC : ' . $notifiable->wallet_ltc)
      ->line('Wallet BTC : ' . $notifiable->wallet_btc)
      ->line('Wallet Camel : ' . $notifiable->wallet_camel)
      ->line('Please click the button below to verify your email address.')
      ->action('Verify Email Address', $verificationUrl)
      ->line('If you did not create an account, no further action is required.');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }

  /**
   * Get the verification URL for the given notifiable.
   *
   * @param mixed $notifiable
   * @return string
   */
  protected function verificationUrl($notifiable)
  {
    if (static::$createUrlCallback) {
      return call_user_func(static::$createUrlCallback, $notifiable);
    }

    return URL::temporarySignedRoute(
      'verification.verify',
      Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
      [
        'id' => $notifiable->getKey(),
        'hash' => sha1($notifiable->getEmailForVerification()),
      ]
    );
  }
}
