<?php

namespace App\Admin;

interface AdminWidgetInterface
{
    public function render(): string;

    public function renderMenu(): string;
}
