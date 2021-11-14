<?php

namespace App\Admin\Tables;

use Framework\Database\Table;
use stdClass;

class SettingTable extends Table
{
    protected string $table = "setting";

    protected ?string $entity = stdClass::class;

    public function getKeyValue(string $keyName): ?stdClass
    {
        $sql = "SELECT * FROM $this->table WHERE keyName = '$keyName'";
        $prep = $this->getPdo()->prepare($sql);
        $prep->execute();
        $result = $prep->fetch();
        if (is_bool($result)) {
            return null;
        }
        return $result;
    }

    public function updateKey(string $key, string $value): bool
    {
        $statement = $this->getPdo()->prepare("UPDATE " . $this->table . " SET keyValue = '$value' WHERE keyName = '$key'");
        return $statement->execute();
    }
}