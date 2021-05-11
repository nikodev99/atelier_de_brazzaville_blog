<?php

use App\Blog\Table\PostTable;
use App\Contact\Action\ContactAction;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;

use function DI\create;
use function DI\get;

return [
    'contact.to'    =>  get('mail.to'),
    ContactAction::class    =>  create()->constructor(
        get('contact.to'),
        get(RendererInterface::class),
        get(FlashService::class),
        get(Swift_Mailer::class),
        get(PostTable::class)
    )
];
