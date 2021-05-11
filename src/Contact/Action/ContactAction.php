<?php

namespace App\Contact\Action;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class ContactAction
{
    private RendererInterface $renderer;

    private PostTable $postTable;

    private string $to;

    private FlashService $flash;

    private Swift_Mailer $mailer;

    /**
     * ContactAction constructor.
     * @param string $to
     * @param RendererInterface $renderer
     * @param FlashService $flash
     * @param Swift_Mailer $mailer
     * @param PostTable $postTable
     */
    public function __construct(string $to, RendererInterface $renderer, FlashService $flash, Swift_Mailer $mailer, PostTable $postTable)
    {
        $this->to = $to;
        $this->renderer = $renderer;
        $this->flash = $flash;
        $this->mailer = $mailer;
        $this->postTable = $postTable;
    }


    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $validator = (new Validator($params))
                ->required('name', 'email', "subject", 'message')
                ->length('name', 5)
                ->length('message', 15)
                ->email('email');
            if ($validator->isValid()) {
                $body = $this->renderer->render('@contact/email/contact.html', $params);
                $message = (new Swift_Message())
                    ->setSubject($params['subject'])
                    ->setBody($body, 'text/html', 'utf-8')
                    ->setFrom('contact@latelierbrazzaville.com', $params['name'])
                    ->setTo($this->to);
                $this->mailer->send($message);
                $this->flash->success("Merci pour votre email. Nous vous contacterons dans le plus bref délai.");
                return new RedirectResponse((string)$request->getUri());
            } else {
                $this->flash->error("Le mail n'a pas été envoyé");
                $errors = $validator->getErrors();
                return $this->contact($errors);
            }
        }
        return $this->contact();
    }

    private function contact(array $params = []): string
    {
        return (new PostIndexAction($this->renderer, $this->postTable))->contact($params);
    }
}
