<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tipos de solicitud disponibles
    |--------------------------------------------------------------------------
    |
    | Para el MVP los dejamos en config.
    | Los keys se guardan en hotel_requests.type_key, por eso no deben renombrarse
    | cuando ya haya datos reales.
    |
    */

    'request_types' => [

        'towels' => [
            'label' => 'Toallas',
            'icon' => '🧺',
            'description' => 'Solicitar toallas',
        ],

        'cleaning' => [
            'label' => 'Limpieza',
            'icon' => '🧹',
            'description' => 'Solicitar limpieza',
        ],

        'maintenance' => [
            'label' => 'Mantenimiento',
            'icon' => '🔧',
            'description' => 'Reportar una falla',
        ],

        'amenity' => [
            'label' => 'Agua / Amenidad',
            'icon' => '💧',
            'description' => 'Solicitar agua o amenidad',
        ],

        'luggage' => [
            'label' => 'Equipaje',
            'icon' => '🧳',
            'description' => 'Apoyo con equipaje',
        ],

        'wakeup' => [
            'label' => 'Despertador',
            'icon' => '⏰',
            'description' => 'Solicitar llamada de despertador',
        ],

        'taxi' => [
            'label' => 'Taxi',
            'icon' => '🚕',
            'description' => 'Solicitar taxi',
        ],

        'suggestion' => [
            'label' => 'Sugerencia',
            'icon' => '💬',
            'description' => 'Enviar una sugerencia o comentario',
        ],

        'other' => [
            'label' => 'Otro',
            'icon' => '✍️',
            'description' => 'Otra solicitud',
        ],
    ],

    'statuses' => [
        'pending' => 'Pendiente',
        'in_progress' => 'En proceso',
        'completed' => 'Resuelta',
        'canceled' => 'Cancelada',
    ],

];