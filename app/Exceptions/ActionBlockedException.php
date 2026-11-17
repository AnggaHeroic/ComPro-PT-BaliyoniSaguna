<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Aksi ditolak oleh aturan bisnis (mis. menghapus kategori yang masih berisi aktivitas).
 * Pesannya sengaja ditulis untuk admin, sehingga aman ditampilkan langsung.
 */
class ActionBlockedException extends RuntimeException {}
