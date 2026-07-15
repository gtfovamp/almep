<?php

namespace App\Http\Controllers\Api;

use App\Models\Consultation;
use App\Mail\ConsultationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ConsultationController extends BaseCrudController
{
    protected string $modelClass = Consultation::class;
    protected string $orderBy = 'created_at';
    protected string $orderDir = 'desc';

    /** Public: create a consultation request from the website. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
        ]);
        $data['created_at'] = Carbon::now();
        $item = Consultation::create($data);

        // Отправляем уведомление на почту (не роняем заявку при сбое SMTP)
        try {
            $to = config('mail.consultation_to') ?: config('mail.from.address');
            if ($to) {
                Mail::to($to)->send(new ConsultationMail($item));
            }
        } catch (\Throwable $e) {
            Log::error('Consultation mail failed: ' . $e->getMessage());
        }

        return response()->json($item, 201);
    }
}
