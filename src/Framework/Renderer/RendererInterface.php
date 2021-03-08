<?php

namespace Framework\Renderer;

interface RendererInterface
{

    /**
     * This function helps to add a path with a namespace.
     * @param string $namespace The namespace of a path
     * @param string|null $path The path to add
     * @return void This function only sereves to fill the paths[]
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * This function helps to render a view.
     * @param string $view The view to render
     * @param string[] $params the parameters to pass to that view.
     */
    public function render(string $view, array $params = []): string;

    /**
     * This function helps to put a variable global to our project.
     * @param string $key The key of the globals table.
     * @param mixed $value The value of that key.
     */
    public function addGlobal(string $key, $value): void;
}
