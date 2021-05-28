<?php

use App\Shop\ShopWidget;

use function DI\add;
use function DI\get;

return [
  "admin.widgets"    =>  add([
      get(ShopWidget::class)
  ]),
];
