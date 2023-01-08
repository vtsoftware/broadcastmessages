#!/usr/bin/php8.0
<?php declare(strict_types=1);

namespace {
  use \VtSoftware\Tools\BroadcastMessages\BroadcastClient;

  require_once(realpath(__DIR__.'/..').'/broadcast-client.php');

  BroadcastClient::init()->onReply(function(String $message) {
    echo 'server reply: '.$message."\n";
  })->send('client data to server');
}
