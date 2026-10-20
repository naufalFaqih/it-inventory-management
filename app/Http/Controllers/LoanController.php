<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnLoanRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Asset;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService
    ) {}

    public function index(Request $request): View
    {
        $query = Loan::with(['asset', 'borrower', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('borrower_id')) {
            $query->where('borrower_id', $request->input('borrower_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%");
            })->orWhereHas('borrower', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $loans = $query->latest('borrowed_at')->paginate(15)->withQueryString();
        $borrowers = User::where('is_active', true)->orderBy('name')->get();

        return view('loans.index', compact('loans', 'borrowers'));
    }

    public function create(Request $request): View
    {
        $selectedAsset = $request->filled('asset_id') ? Asset::find($request->input('asset_id')) : null;
        // Only available assets can be borrowed (BR-03)
        $availableAssets = Asset::where('status', Asset::STATUS_AVAILABLE)->orderBy('name')->get();
        $borrowers = User::where('is_active', true)->orderBy('name')->get();

        return view('loans.create', compact('selectedAsset', 'availableAssets', 'borrowers'));
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $asset = Asset::findOrFail($request->input('asset_id'));

        $loan = $this->loanService->checkout(
            $asset,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('loans.index')->with('success', 'Peminjaman aset berhasil dicatat.');
    }

    public function returnForm(Loan $loan): View
    {
        if ($loan->status !== Loan::STATUS_ACTIVE) {
            return redirect()->route('loans.index')->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        return view('loans.return', compact('loan'));
    }

    public function returnProcess(ReturnLoanRequest $request, Loan $loan): RedirectResponse
    {
        $this->loanService->checkin(
            $loan,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('loans.index')->with('success', 'Pengembalian perangkat berhasil diproses.');
    }
}
