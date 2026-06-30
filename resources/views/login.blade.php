<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Almep Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Montserrat','sans-serif']},colors:{brand:{500:'#6366f1',600:'#4f46e5',700:'#4338ca'}}}}}</script>
</head>
<body class="flex min-h-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 font-sans p-4">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Almep Admin</h1>
            <p class="mt-1 text-sm text-slate-500">Войдите в панель управления</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Логин</label>
                    <input name="username" type="text" autofocus value="{{ old('username') }}"
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20" required>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700">Пароль</label>
                    <input name="password" type="password"
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20" required>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                    Войти
                </button>
            </form>
        </div>
        <p class="mt-6 text-center text-xs text-slate-400">© {{ date('Y') }} Almep Trading</p>
    </div>
</body>
</html>
