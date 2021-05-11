<?php

namespace Framework\Mail;

use Psr\Container\ContainerInterface;
use Swift_Mailer;
use Swift_SmtpTransport;

class SwiftMailerFactory
{
    public function __invoke(ContainerInterface $container): Swift_Mailer
    {
        $transport = (new Swift_SmtpTransport("mail.infomaniak.com", 465, 'ssl'))
            ->setUsername("contact@latelierbrazzaville.com")
            ->setPassword("Karine33")
        ;
        return new Swift_Mailer($transport);
    }
}
