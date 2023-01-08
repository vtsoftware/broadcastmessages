#!/usr/bin/php8.0
<?php declare(strict_types=1);

namespace {
  use \VtSoftware\Tools\BroadcastMessages\BroadcastServer;

  require_once(realpath(__DIR__.'/..').'/broadcast-server.php');

  BroadcastServer::bind()->onReceive(function(array $message) {
    echo 'received data: ';
    var_dump($message);
    echo "\n";

    $result = -1;

    if ($message['action'] == 'sum') {
      $num_a = (int)$message['data'][0];
      $num_b = (int)$message['data'][1];

      $result = ($num_a + $num_b);
    }

    return array(
      'result' => true,
      'action' => $message['action'],
      'result' => $result
    );
  })->run();
}
