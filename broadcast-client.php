#!/usr/bin/php8.0
<?php declare(strict_types=1);
namespace VtSoftware\Tools\BroadcastMessages;

class BroadcastClient {
  private String $ip;
  private int $port;
  private $socket;
  private ?\Closure $replyCallback = null;
  public static int $message_length = 1024;
  public static int $timeout = 1;

  public function __construct(String $ip = '255.255.255.255', int $port = 8853) {
    $this->ip = $ip;
    $this->port = $port;

    $this->socket = \socket_create(\AF_INET, \SOCK_DGRAM, \SOL_UDP);
    \socket_set_option($this->socket, \SOL_SOCKET, \SO_BROADCAST, 1);
    \socket_set_option($this->socket, \SOL_SOCKET, \SO_RCVTIMEO, array(
      'sec' => static::$timeout,
      'usec' => 0
    ));
  }
  public static function init(): static {
    $instance = new static();
    return $instance;
  }
  public function onReply(\Closure $callback): static {
    $this->replyCallback = $callback;
    return $this;
  }
  public function send(String $message): static {
    $message = trim($message);
    \socket_sendto($this->socket, $message, \strlen($message), 0, $this->ip, $this->port);

    $reply_buffer = '';
    @\socket_recv($this->socket, $reply_buffer, static::$message_length, 0);

    if ($reply_buffer !== null && $this->replyCallback !== null) {
      $reply_buffer = \trim($reply_buffer);
      call_user_func($this->replyCallback, $reply_buffer);
    }

    \socket_close($this->socket);

    return $this;
  }
}

BroadcastClient::init()->onReply(function(String $message) {
  echo 'szerver valasza: '.$message."\n";
})->send('kliens kuldi szervernek');
