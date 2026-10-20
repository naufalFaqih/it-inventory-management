<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /**
     * Borrow an asset (BR-03, BR-08).
     */
    public function checkout(Asset $asset, array $data, User $actor): Loan
    {
        // BR-03: Aset yang sedang dipinjam atau menjalani maintenance tidak dapat dipinjamkan
        if ($asset->status === Asset::STATUS_BORROWED) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset saat ini sedang dipinjam dan tidak dapat dipinjamkan kembali.',
            ]);
        }

        if ($asset->status === Asset::STATUS_MAINTENANCE) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset sedang dalam proses pemeliharaan/perbaikan dan tidak dapat dipinjamkan.',
            ]);
        }

        if ($asset->status === Asset::STATUS_RETIRED) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset sudah dinonaktifkan (retired) dan tidak dapat dipinjamkan.',
            ]);
        }

        if ($asset->loans()->where('status', Loan::STATUS_ACTIVE)->exists()) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset masih memiliki transaksi peminjaman aktif yang belum dikembalikan.',
            ]);
        }

        return DB::transaction(function () use ($asset, $data, $actor) {
            $loan = Loan::create([
                'asset_id' => $asset->id,
                'borrower_id' => $data['borrower_id'],
                'borrowed_at' => $data['borrowed_at'] ?? now(),
                'due_at' => $data['due_at'] ?? null,
                'checkout_condition' => $data['checkout_condition'] ?? $asset->condition,
                'status' => Loan::STATUS_ACTIVE,
                'notes' => $data['notes'] ?? null,
                'created_by' => $actor->id,
            ]);

            $asset->update([
                'status' => Asset::STATUS_BORROWED,
            ]);

            AuditLog::log('loan_checkout', $asset, [
                'status' => Asset::STATUS_AVAILABLE,
            ], [
                'status' => Asset::STATUS_BORROWED,
                'loan_id' => $loan->id,
                'borrower_id' => $data['borrower_id'],
            ], $actor->id);

            return $loan;
        });
    }

    /**
     * Return a borrowed asset (BR-05, BR-08).
     */
    public function checkin(Loan $loan, array $data, User $actor): Loan
    {
        // BR-05: Pengembalian hanya dapat dilakukan pada transaksi peminjaman yang masih aktif
        if ($loan->status !== Loan::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'loan' => 'Pengembalian hanya dapat dilakukan untuk transaksi peminjaman yang masih aktif.',
            ]);
        }

        return DB::transaction(function () use ($loan, $data, $actor) {
            $asset = $loan->asset;
            $returnCondition = $data['return_condition'] ?? Asset::CONDITION_GOOD;

            $loan->update([
                'returned_at' => $data['returned_at'] ?? now(),
                'return_condition' => $returnCondition,
                'status' => Loan::STATUS_RETURNED,
                'notes' => ! empty($data['notes']) ? ($loan->notes ? $loan->notes . "\n" : '') . 'Pengembalian: ' . $data['notes'] : $loan->notes,
            ]);

            // Determine next status based on return condition (PRD 9.3 step 4)
            $nextStatus = ($returnCondition === Asset::CONDITION_MAJOR_DAMAGE)
                ? Asset::STATUS_MAINTENANCE
                : ($asset->assigned_to ? Asset::STATUS_ASSIGNED : Asset::STATUS_AVAILABLE);

            $asset->update([
                'status' => $nextStatus,
                'condition' => $returnCondition,
            ]);

            AuditLog::log('loan_checkin', $asset, [
                'status' => Asset::STATUS_BORROWED,
            ], [
                'status' => $nextStatus,
                'condition' => $returnCondition,
                'loan_id' => $loan->id,
            ], $actor->id);

            return $loan;
        });
    }
}
