<?php
// [multimod] messages de validation en français (règles utilisées par le panneau)

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'string' => 'Le champ :attribute doit être un texte.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'array' => 'Le champ :attribute doit être une liste.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'json' => 'Le champ :attribute doit être du JSON valide.',
    'in' => 'La valeur choisie pour :attribute n\'est pas valide.',
    'exists' => 'La valeur choisie pour :attribute n\'existe pas.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'array' => 'Le champ :attribute ne doit pas avoir plus de :max éléments.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit valoir au moins :min.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'string' => 'Le champ :attribute doit faire au moins :min caractères.',
        'array' => 'Le champ :attribute doit avoir au moins :min éléments.',
    ],
    'between' => [
        'numeric' => 'Le champ :attribute doit être entre :min et :max.',
        'string' => 'Le champ :attribute doit faire entre :min et :max caractères.',
    ],
    'attributes' => [
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'name' => 'nom',
        'display_name' => 'nom affiché',
        'price' => 'prix',
        'credits' => 'crédits',
        'category_id' => 'catégorie',
        'import' => 'fichier à importer',
    ],
];
