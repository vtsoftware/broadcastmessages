#!/usr/bin/php8.0
<?php declare(strict_types=1);

namespace {
  use \VtSoftware\Tools\BroadcastMessages\BroadcastServer;

  require_once(realpath(__DIR__.'/..').'/broadcast-server.php');

  BroadcastServer::bind()->onReceive(function(String $message) {
    echo 'received data: ';
    var_dump($message);
    echo "\n";

    return 'server reply to client';
  })->run();
}
