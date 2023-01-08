<?php declare(strict_types=1);
namespace VtSoftware\Tools\BroadcastMessages;

class BroadcastServer {
  private String $ip;
  private int $port;
  private $socket;
  private \Closure $receiveCallback;
  public static int $message_length = 1024;

  public static function check_parse_json(array|String &$data): void {
    if (is_array($data)) {
      $data = json_encode($data, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    } else if (is_string($data)) {
      $data_fc = \substr($data, 0, 1);
      if ($data_fc === '[' || $data_fc === '{') {
        $data = \json_decode($data, true);
      }
    }
  }

  public function __construct(String $ip = '255.255.255.255', int $port = 8853) {
    $this->ip = $ip;
    $this->port = $port;

    $this->socket = \socket_create(\AF_INET, \SOCK_DGRAM, \SOL_UDP);
    socket_set_option($this->socket, \SOL_SOCKET, \SO_BROADCAST, 1);
    socket_set_option($this->socket, \SOL_SOCKET, \SO_REUSEADDR, 1);
    socket_set_option($this->socket, \SOL_SOCKET, \SO_REUSEPORT, 1);

    \socket_bind($this->socket, $this->ip, $this->port);
  }
  public static function bind(): static {
    $instance = new static();
    return $instance;
  }
  public function onReceive(\Closure $callback): static {
    $this->receiveCallback = $callback;
    return $this;
  }
  public function run(): void {
    while (1) {
      if (@\socket_recvfrom($this->socket, $buffer, static::$message_length, 0, $ip, $port)) {
        static::check_parse_json($buffer);

        if ($this->receiveCallback !== null) {
          $reply = call_user_func($this->receiveCallback, $buffer);

          if ($reply !== null) {
            static::check_parse_json($reply);

            $reply = trim($reply);
            \socket_sendto($this->socket, $reply, strlen($reply), 0, $ip, $port);
          }
        }
      }
    }
  }
}
