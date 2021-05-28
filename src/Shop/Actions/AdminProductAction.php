<?php

namespace App\Shop\Actions;

use App\Shop\Entity\Product;
use App\Shop\Table\ProductsTable;
use App\Shop\Upload\UploadProductImage;
use App\Shop\Upload\UploadProductPdf;
use Framework\Actions\CrudAction;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminProductAction extends CrudAction
{
    private ProductsTable $table;

    protected string $viewPath = '@shop/admin/products';

    protected string $routePrefix = 'admin.shop.product';

    protected array $acceptedParams = ['name', 'description', 'slug', 'price', 'created_at', 'image', 'updated_at'];

    protected array $success_messages = [
        'create'    =>  "Nouveau produit ajoutée avec succès !",
        'edit'      =>  "Produit modifiée avec succès !",
        'delete'    =>  "Produit supprimée avec succès !"
    ];

    protected array $failed_messages = [
        'create'   =>  'Le système d\'ajout de produit à rencontré une ou plusieurs erreurs',
        'edit'   =>  'Le système de modification de produit à rencontré une ou plusieurs erreurs',
        'delete'   =>  'Le système de suppression de produit à rencontré une ou plusieurs erreurs'
    ];

    private UploadProductImage $uploadImage;
    private UploadProductPdf $uploadPdf;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        ProductsTable $table,
        FlashService $flash,
        UploadProductImage $uploadImage,
        UploadProductPdf $uploadPdf
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->table = $table;
        $this->uploadImage = $uploadImage;
        $this->uploadPdf = $uploadPdf;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Product $item
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $item = null): array
    {
        $clientFilename = $request->getUploadedFiles()['image']->getClientFilename();
        $params = $request->getParsedBody();
        if (!empty($clientFilename)) {
            $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
            if (!is_null($item)) {
                $params['image'] = $this->uploadImage->upload($params["image"], $item->getImage());
            } else {
                $params['image'] = $this->uploadImage->upload($params["image"]);
            }
        }
        $params = array_filter($params, function ($key) {
            return in_array($key, $this->acceptedParams);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at'  =>  date("Y-m-d H:i:s"),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Product $item
     */
    protected function pdfPersist(ServerRequestInterface $request, $item): void
    {
        $clientFilename = $request->getUploadedFiles()['pdf']->getClientFilename();
        if (!empty($clientFilename)) {
            $pdfFile = $request->getUploadedFiles()['pdf'];
            $productId = $item->getId() === 0 ? $this->table->getPdo()->lastInsertId() : $item->getId();
            $this->uploadPdf->upload($pdfFile, "$productId.pdf", "$productId.pdf");
        }
    }

    /**
     * @throws NoRecordException
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Product $product */
        $product = $this->table->find($request->getAttribute('id'));
        $this->uploadImage->delete($product->getImage());
        $this->uploadPdf->delete($product->getPdf());
        return parent::delete($request);
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        $validator =  parent::getValidator($request)
            ->unEmptied('name', 'description', 'slug', 'price')
            ->required('name', 'description', 'slug', 'price', 'created_at')
            ->length('name', 3, 250)
            ->length('slug', 3, 50)
            ->length('description', 10)
            ->slug('slug')
            ->unique('slug', $this->table)
            ->datetime('created_at')
            ->numeric('price')
            ->extension('image', ['jpg', 'png'])
            ->extension('pdf', ['pdf'])
        ;
        if ($request->getAttribute('id') === null) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
