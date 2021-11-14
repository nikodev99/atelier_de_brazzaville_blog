<?php

namespace App\Admin\Tables;

use Framework\Database\Table;
use stdClass;

class MessageTable extends Table
{
    protected string $table = "message";

    protected ?string $entity = stdClass::class;

    public function getMessage(): ?stdClass
    {
        $request = "SELECT * FROM message WHERE id = 1";
        $result = $this->getPdo()->prepare($request);
        $result->execute();
        $content = $result->fetch();
        if (is_bool($content)) {
            return null;
        }
        return $content;
    }
}
