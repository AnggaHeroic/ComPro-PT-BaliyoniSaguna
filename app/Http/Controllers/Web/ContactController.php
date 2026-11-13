<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    // Profil perusahaan ($company) sudah tersedia di semua view publik. 'perihal' (opsional, dari tombol di detail aktivitas) mengisi kolom perihal formulir.
    public function index(Request $request): View
    {
        return view('web.contact', [
            'subject' => Str::limit(trim((string) $request->query('perihal')), 100, ''),
        ]);
    }
}
