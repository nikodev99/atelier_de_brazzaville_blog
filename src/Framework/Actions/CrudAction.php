<?php

namespace Framework\Actions;

use Framework\Database\NoRecordException;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CrudAction
{
    use RouterAwareAction;

    private RendererInterface $renderer;

    private Router $router;

    private Table $table;

    private FlashService $flash;

    protected string $viewPath;

    protected string $routePrefix;

    protected array $success_messages =  [
        'create'    =>  "Nouveau article ajouté avec succès !",
        'edit'      =>  "Article modifié avec succès !",
        'delete'    =>  "Article supprimer avec succès !"
    ];

    protected array $failed_messages = [
        'create'   =>  'Le système d\'ajout d\'article à rencontré une ou plusieurs erreurs',
        'edit'   =>  'Le système de modification à rencontrée une ou plusieurs erreurs',
        'delete'   =>  'Le système de suppression d\'article à rencontré une ou plusieurs erreurs'
    ];

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->table = $table;
        $this->flash = $flash;
    }

    /**
     * @throws NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->renderer->addGlobal("viewPath", $this->viewPath);
        $this->renderer->addGlobal("routePrefix", $this->routePrefix);
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (
            substr($request->getUri()->getPath(), -5) === 'posts' ||
            substr($request->getUri()->getPath(), -3) === 'ies' ||
            substr($request->getUri()->getPath(), -3) === 'cts'
        ) {
            return $this->posts();
        }
        if (substr($request->getUri()->getPath(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
    }

    public function posts(): string
    {
        $items = $this->table->findAll();
        return $this->renderer->render($this->viewPath . '/posts', compact('items'));
    }

    /**
     * @throws NoRecordException
     */
    public function create(ServerRequestInterface $request): string
    {
        $errors = [];
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            //dd($request->getUploadedFiles(), $params);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $id = $this->table->add($this->getParams($request, $item));
                $this->pdfPersist($request, $item);
                $item = $this->table->find($id);
                $this->flash->success($this->success_messages['create']);
                return $this->renderer->render($this->viewPath . '/create', $this->formParam(compact('item')));
            }
            $errors = $validator->getErrors();
            $item = $request->getParsedBody();
            $this->flash->error($this->failed_messages['create']);
        }
        return $this->renderer->render($this->viewPath . '/create', $this->formParam(compact('item', 'errors')));
    }

    /**
     * @throws NoRecordException
     */
    public function edit(ServerRequestInterface $request)
    {
        $errors = [];
        $id = (int)$request->getAttribute('id');
        $item = $this->table->find($id);
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $params = $this->getParams($request, $item);
                if (property_exists($item, 'view')) {
                    $params['view'] = $item->view;
                }
                if (empty($params['image'])) {
                    unset($params['image']);
                }
                unset($params['created_date']);
                $this->table->update($id, $params);
                $this->pdfPersist($request, $item);
                $this->flash->success($this->success_messages['edit']);
                return $this->redirect($this->routePrefix . '.edit', ['id' => $id]);
            }
            $errors = $validator->getErrors();
            $params = $request->getParsedBody();
            $params['id'] = $id;
            $item = $params;
            $this->flash->error($this->failed_messages['edit']);
        }
        return $this->renderer->render($this->viewPath . '/edit', $this->formParam(compact('item', 'errors')));
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $delete = $this->table->delete($request->getAttribute('id'));
        if ($delete) {
            $this->flash->success($this->success_messages['delete']);
        } else {
            $this->flash->error($this->failed_messages['delete']);
        }
        return $this->redirect($this->routePrefix . '.posts');
    }

    protected function getNewEntity()
    {
        $entity = $this->table->getEntity();
        return new $entity();
    }

    protected function getParams(ServerRequestInterface $request, $item = null): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    protected function formParam(array $params): array
    {
        return $params;
    }

    protected function pdfPersist(ServerRequestInterface $request, $item): void
    {
    }
}
