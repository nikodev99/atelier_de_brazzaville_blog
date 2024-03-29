<?php

namespace Framework\Validator;

class ValidationError
{
    private string $key;

    private string $rule;

    private array $attributes;

    private array $messages = [
        'required'  =>  'Le champ %s est requis',
        'empty'  =>  'Le champ %s ne peut être vide',
        'slug'  =>  'Le champ %s n\'est pas un slug valide',
        'between'   => 'Le champ %s doit contenir au moins plus de %d caractères et au plus moins de %d caractères',
        'min'   =>  'Le champ %s doit contenir au moins plus de %d caractères',
        'max'   =>  'Le champ %s doit contenir au plus moins de %d caractères',
        'datetime'  => 'Le champ %s doit être une date valide (%s)',
        'exists'  => 'L\'enregistrement choisi dans le champ %s n\'existe pas dans le système',
        'unique'    =>  'L\'enregistrement choisi dans le champ %s existe déjà dans le système',
        'fileType'  =>  'Le champs %s n\'est pas au format valide. type attendu %s',
        'uploaded'  =>  'Vous devez uploader un fichier',
        'email'     =>  "Email non valide",
        'confirm'   =>  "%s et %s_confirm ne sont conformes"
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString(): string
    {
        if (!array_key_exists($this->rule, $this->messages)) {
            return "Le champ {$this->key} ne correspond pas à la règle {$this->rule}";
        }
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string) call_user_func_array('sprintf', $params);
    }
}
