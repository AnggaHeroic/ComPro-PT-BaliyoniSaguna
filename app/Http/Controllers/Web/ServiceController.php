<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ServiceController extends Controller
{
    // Daftar layanan ($services) dan profil ($company) sudah tersedia di semua view publik.
    public function index(): View
    {
        return view('web.services');
    }
}
