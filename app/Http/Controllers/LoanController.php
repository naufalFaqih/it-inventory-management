<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $loans = Loan::with(['asset', 'borrower', 'creator'])->latest('borrowed_at')->paginate(15);
        return view('loans.index', compact('loans'));
    }
}
