<?php

namespace RabbitJump\Commands;

use PhpAmqpLib\Channel\AMQPChannel;

abstract class BaseExchangerConsumerCommand extends WaitingConsumerCommand
{

    protected $exchanger = [
        'name' => 'basic',
        'type' => 'fanout',
        'passive' => false,
        'durable' => false,
        'auto_delete' => false,
    ];

    protected $queueName;

    protected function connectToQueue(AMQPChannel $channel): void
    {
        $channel->exchange_declare(
            $this->exchanger['name'],
            $this->exchanger['type'],
            $this->exchanger['passive'],
            $this->exchanger['durable'],
            $this->exchanger['auto_delete']
        );
        list($this->queueName, ,) = $channel->queue_declare("");
        $channel->queue_bind($this->queueName, $this->exchanger['name']);
    }

    protected function consumeMessage(AMQPChannel $channel, \Closure $callback): void
    {
        $channel->basic_consume($this->queueName, '', false, true, false, false, $callback);
    }
}