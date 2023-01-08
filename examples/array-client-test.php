#!/usr/bin/php8.0
<?php declare(strict_types=1);

namespace {
  use \VtSoftware\Tools\BroadcastMessages\BroadcastClient;

  require_once(realpath(__DIR__.'/..').'/broadcast-client.php');

  BroadcastClient::init()->onReply(function(array|String $message) {
    echo 'server reply: ';
    var_dump($message);
    echo "\n";
  })->send(array(
    'action' => 'sum',
    'data' => array(12, 55)
  ));
}
