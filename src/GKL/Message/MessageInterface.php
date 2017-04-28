<?php
namespace GKL\Message;


interface MessageInterface
{
    public function send($phone, $templateId, array $content);
}