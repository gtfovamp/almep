<?php

namespace App\Http\Controllers\Admin;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationWebController extends AdminCrudController
{
    protected string $modelClass = Consultation::class;
    protected string $routeBase = 'consultations';
    protected string $active = 'consultations';
    protected string $titleSingular = 'Заявка';
    protected string $titlePlural = 'Заявки';

    public function index()
    {
        $items = Consultation::orderByDesc('created_at')->paginate(24);
        return view('admin.consultations.index', ['items' => $items]);
    }

    protected function columns(): array { return []; }
    protected function fields(): array { return []; }
}
