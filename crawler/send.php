<?php
if (count($argv) == 2) {
  require_once __DIR__ . '/vendor/autoload.php';
  use PhpAmqpLib\Connection\AMQPConnection;
  use PhpAmqpLib\Message\AMQPMessage;

  $connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
  $channel = $connection->channel();

  $channel->queue_declare('crawl', false, false, false, false);

  $msg = new AMQPMessage($argv[1],
                        array('delivery_mode' => 2) # make message persistent
                      );

  $channel->basic_publish($msg, '', 'crawl');
}
