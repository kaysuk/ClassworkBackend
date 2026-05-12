<?php

function getValidationRules() {
    return [
        'name' => [
            'pattern' => "/^[a-zA-Zа-яА-Я\s]{1,150}$/u",
            'message' => "Некорректное ФИО",
            'allowed' => "Допустимо: буквы и пробелы"
        ],
        'phone' => [
            'pattern' => "/^[0-9+\-\s()]{5,20}$/",
            'message' => "Некорректный телефон",
            'allowed' => "Допустимо: цифры, +, -, скобки и пробелы"
        ],
        'email' => [
            'pattern' => "/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
            'message' => "Некорректный email",
            'allowed' => "Формат: name@example.com"
        ],
        'birthdate' => [
            'pattern' => null,
            'message' => "Укажите дату рождения",
            'allowed' => "Формат: YYYY-MM-DD"
        ],
        'bio' => [
            'pattern' => "/^[a-zA-Zа-яА-Я0-9\s.,!?-]{10,500}$/u",
            'message' => "Некорректная биография",
            'allowed' => "Допустимо: буквы, цифры, пробелы, . , ! ? -"
        ]
    ];
}

function validateFormData($data) {
    $rules = getValidationRules();
    $errors = [];
    $errorFields = [];
    
    // Валидация ФИО
    if (empty($data['name'])) {
        $errors[] = "ФИО не заполнено";
        $errorFields['name'] = $rules['name']['allowed'];
    } elseif (!preg_match($rules['name']['pattern'], $data['name'])) {
        $errors[] = $rules['name']['message'] . ". " . $rules['name']['allowed'];
        $errorFields['name'] = $rules['name']['allowed'];
    }
    
    // Валидация телефона
    if (empty($data['phone'])) {
        $errors[] = "Телефон не заполнен";
        $errorFields['phone'] = $rules['phone']['allowed'];
    } elseif (!preg_match($rules['phone']['pattern'], $data['phone'])) {
        $errors[] = $rules['phone']['message'] . ". " . $rules['phone']['allowed'];
        $errorFields['phone'] = $rules['phone']['allowed'];
    }
    
    // Валидация email
    if (empty($data['email'])) {
        $errors[] = "Email не заполнен";
        $errorFields['email'] = $rules['email']['allowed'];
    } elseif (!preg_match($rules['email']['pattern'], $data['email'])) {
        $errors[] = $rules['email']['message'] . ". " . $rules['email']['allowed'];
        $errorFields['email'] = $rules['email']['allowed'];
    }
    
    // Валидация даты рождения
    if (empty($data['birthdate'])) {
        $errors[] = $rules['birthdate']['message'];
        $errorFields['birthdate'] = $rules['birthdate']['allowed'];
    }
    
    // Валидация пола
    if (!in_array($data['gender'] ?? '', ['male', 'female'])) {
        $errors[] = "Выберите пол";
        $errorFields['gender'] = "required";
    }
    
    // Валидация языков
    if (empty($data['languages'])) {
        $errors[] = "Выберите хотя бы один язык";
        $errorFields['languages'] = "required";
    }
    
    // Валидация биографии
    if (empty($data['bio'])) {
        $errors[] = "Заполните биографию";
        $errorFields['bio'] = $rules['bio']['allowed'];
    } elseif (!preg_match($rules['bio']['pattern'], $data['bio'])) {
        $errors[] = $rules['bio']['message'] . ". " . $rules['bio']['allowed'];
        $errorFields['bio'] = $rules['bio']['allowed'];
    }
    
    // Валидация контракта
    if (empty($data['contract'])) {
        $errors[] = "Необходимо согласие с контрактом";
        $errorFields['contract'] = "required";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'errorFields' => $errorFields
    ];
}
