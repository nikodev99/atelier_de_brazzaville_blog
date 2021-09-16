<?php

namespace Framework;

use DateTime;
use Framework\Database\Table;
use Framework\Validator\ValidationError;
use PDO;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPE = [
        'jpg'   =>  'image/jpeg',
        'jpeg'   =>  'image/jpeg',
        'png'   =>  'image/png',
        'pdf'   =>  'application/pdf'
    ];

    private array $params;

    private array $errors = [];

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function required(string ...$keys): self
    {
        return $this->requirement($keys, 'required');
    }

    public function unEmptied(string ...$keys): self
    {
        return $this->requirement($keys, 'empty', true);
    }

    public function slug(string ...$keys): self
    {
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (!is_null($value) && !preg_match($pattern, $this->params[$key])) {
                $this->addError($key, 'slug');
            }
        }
        return $this;
    }

    public function numeric(string ...$keys): self
    {
        foreach ($keys as $key) {
            $keyValue = $this->getValue($key);
            if (!is_numeric($keyValue)) {
                $this->addError($key, 'numeric');
            }
        }
        return $this;
    }

    public function length(string $key, ?int $minLength = null, ?int $maxLength = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (
            !is_null($minLength) && !is_null($maxLength) &&
            ($length < $minLength || $length > $maxLength)
        ) {
            $this->addError($key, 'between', [$minLength, $maxLength]);
            return $this;
        }
        if (!is_null($minLength) && $length < $minLength) {
            $this->addError($key, 'min', [$minLength]);
            return $this;
        }
        if (!is_null($maxLength) && $length > $maxLength) {
            $this->addError($key, 'max', [$maxLength]);
        }
        return $this;
    }

    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }

    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $confirmValue = $this->getValue($key . '_confirm');
        if ($value !== $confirmValue) {
            $this->addError($key, 'confirm', [$key]);
        }
        return $this;
    }

    public function datetime(string $key, string $format = 'Y-m-d H:i:s'): self
    {

        $keyValue = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $keyValue);
        $errors = DateTime::getLastErrors();
        $errorValue = 'Data missing';
        if ($errors['error_count'] > 0 || $errors['warning_count'] || $date === false) {
            if (array_key_exists(10, $errors['errors']) && $errors['errors'][10] === $errorValue) {
                return $this;
            } else {
                $this->addError($key, 'datetime', [$format]);
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string|Table $table
     * @param PDO|null $pdo
     * @return $this
     */
    public function exists(string $key, $table, ?PDO $pdo = null): self
    {
        $keyValue = $this->getValue($key);
        $statement = $this->valueExists($key, $table, $pdo);
        $exec = $statement->execute([$keyValue]);
        if ($exec === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string|Table $table
     * @param PDO|null $pdo
     * @return $this
     */
    public function unique(string $key, $table, ?PDO $pdo = null): self
    {
        $keyValue = $this->getValue($key);
        $statement = $this->valueExists($key, $table, $pdo);
        $statement->execute([$keyValue]);
        if ($statement->rowCount() === 1) {
            $this->addError($key, 'unique', [$table]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string|Table $table
     * @param PDO|null $pdo
     * @return $this
     */
    public function acceptUnique(string $key, $table, ?PDO $pdo = null): self
    {
        $keyValue = $this->getValue($key);
        $statement = $this->valueExists($key, $table, $pdo);
        $statement->execute([$keyValue]);
        if ($statement->rowCount() > 1) {
            $this->addError($key, 'unique', [$table]);
        }
        return $this;
    }

    public function uploaded(string $key): self
    {
        /**
         * @var UploadedFileInterface $file
         */
        $file = $this->getValue($key);
        if (is_null($file) || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    public function extension(string $key, array $extensions): self
    {
        /**
         * @var UploadedFileInterface $file
         */
        $file = $this->getValue($key);
        if (!is_null($file) && !empty($file)) {
            if ($file->getError() === UPLOAD_ERR_OK) {
                $type = $file->getClientMediaType();
                $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
                $expected_type = self::MIME_TYPE[$extension] ?? null;
                if (!in_array($extension, $extensions) || $expected_type !== $type) {
                    $this->addError($key, 'fileType', [join(', ', $extensions)]);
                }
            }
        }
        return $this;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function requirement(array $keys, string $rule, bool $notEmpty = false): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            $required = $notEmpty ? is_null($value) || empty($value) : is_null($value);
            if ($required) {
                $this->addError($key, $rule);
            }
        }
        return $this;
    }

    private function valueExists(string $key, $table, ?PDO $pdo = null)
    {
        if ($table instanceof Table) {
            $pdo = $table->getPdo();
            $table = $table->getTable();
        }
        $keyValue = $this->getValue($key);
        $column = $key;
        if (stripos($key, '_id')) {
            $column = 'id';
        }
        return $pdo->prepare("SELECT * FROM $table WHERE $column = ?");
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }
}
