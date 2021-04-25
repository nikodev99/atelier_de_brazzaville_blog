<?php

namespace Framework;

use DateTime;
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

    public function exists(string $key, string $table, PDO $pdo): self
    {
        $keyValue = $this->getValue($key);
        $statement = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $statement->execute([$keyValue]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
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
        if (!is_null($file) && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expected_type = self::MIME_TYPE[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expected_type !== $type) {
                $this->addError($key, 'fileType', [join(', ', $extensions)]);
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
