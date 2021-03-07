<?php

namespace Framework;

class Renderer
{

    private const DEFAULT_NAMESPACE = '__MAIN';
    private const DS = DIRECTORY_SEPARATOR;

    private array $paths = [];
    private array $globals = [];

    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) :
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        else :
            $this->paths[$namespace] = $path;
        endif;
    }

    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) .  '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . self::DS . $view . '.php';
        }
        ob_start();
        extract($this->globals);
        extract($params);
        $renderer = $this;
        require($path);
        return ob_get_clean();
    }

    /**
     * @var mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    private function hasNamespace(string $viewPath): bool
    {
        return $viewPath[0] === '@';
    }

    private function getNamespace(string $viewPath): string
    {
        return substr($viewPath, 1, strpos($viewPath, '/') - 1);
    }

    private function replaceNamespace(string $viewPath): string
    {
        $namespace = $this->getNamespace($viewPath);
        return str_replace('@' . $namespace, $this->paths[$namespace], $viewPath);
    }
}
