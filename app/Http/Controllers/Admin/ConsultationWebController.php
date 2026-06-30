<?php

namespace App\Http\Controllers\Admin;

use App\Models\Consultation;

class ConsultationWebController extends AdminCrudController
{
    protected string $modelClass = Consultation::class;
    protected string $routeBase = 'consultations';
    protected string $active = 'consultations';
    protected string $titleSingular = 'Заявка';
    protected string $titlePlural = 'Заявки';
    protected string $orderBy = 'created_at';
    protected string $orderDir = 'desc';

    protected function columns(): array
    {
        return [
            'name'  => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'created_at' => ['label' => 'Дата', 'type' => 'date'],
        ];
    }
    protected function fields(): array { return []; } // read-only (view + delete)
}
