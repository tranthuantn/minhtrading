<?php

namespace Database\Seeders;

use App\Models\TuitionPayment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TuitionSampleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $students = User::where('role', 'student')->orderBy('id')->get();
        if ($students->count() === 0) {
            return;
        }

        // Student 1: Đã đóng đủ, còn 25 ngày
        if (isset($students[0])) {
            $s1 = $students[0];
            $start = now()->subDays(5);
            $end = now()->addDays(25);
            $s1->update([
                'billing_type' => 'monthly',
                'monthly_tuition_fee' => 2000000,
                'paid_until' => $end->toDateString(),
            ]);
            TuitionPayment::firstOrCreate(
                ['user_id' => $s1->id, 'transaction_code' => 'TCB-261001'],
                [
                    'admin_id' => $admin->id,
                    'amount' => 2000000,
                    'months_count' => 1,
                    'billing_type' => 'monthly',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'transaction_code' => 'TCB-261001',
                    'note' => 'Học phí Tháng 10/2026',
                    'created_at' => $start,
                ]
            );
        }

        // Student 2: Sắp hết hạn trong 3 ngày
        if (isset($students[1])) {
            $s2 = $students[1];
            $start = now()->subDays(27);
            $end = now()->addDays(3);
            $s2->update([
                'billing_type' => 'monthly',
                'monthly_tuition_fee' => 2000000,
                'paid_until' => $end->toDateString(),
            ]);
            TuitionPayment::firstOrCreate(
                ['user_id' => $s2->id, 'transaction_code' => 'TCB-260920'],
                [
                    'admin_id' => $admin->id,
                    'amount' => 2000000,
                    'months_count' => 1,
                    'billing_type' => 'monthly',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'transaction_code' => 'TCB-260920',
                    'note' => 'Học phí chu kỳ gần nhất',
                    'created_at' => $start,
                ]
            );
        }

        // Student 3: Chưa nộp tháng này (quá hạn 8 ngày)
        if (isset($students[2])) {
            $s3 = $students[2];
            $start = now()->subDays(38);
            $end = now()->subDays(8);
            $s3->update([
                'billing_type' => 'monthly',
                'monthly_tuition_fee' => 2000000,
                'paid_until' => $end->toDateString(),
            ]);
            TuitionPayment::firstOrCreate(
                ['user_id' => $s3->id, 'transaction_code' => 'MB-260910'],
                [
                    'admin_id' => $admin->id,
                    'amount' => 2000000,
                    'months_count' => 1,
                    'billing_type' => 'monthly',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'transaction_code' => 'MB-260910',
                    'note' => 'Học phí tháng trước',
                    'created_at' => $start,
                ]
            );
        }

        // Student 4: Nợ nhiều tháng (quá hạn 45 ngày)
        if (isset($students[3])) {
            $s4 = $students[3];
            $start = now()->subDays(75);
            $end = now()->subDays(45);
            $s4->update([
                'billing_type' => 'monthly',
                'monthly_tuition_fee' => 2000000,
                'paid_until' => $end->toDateString(),
            ]);
            TuitionPayment::firstOrCreate(
                ['user_id' => $s4->id, 'transaction_code' => 'VCB-260805'],
                [
                    'admin_id' => $admin->id,
                    'amount' => 2000000,
                    'months_count' => 1,
                    'billing_type' => 'monthly',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'transaction_code' => 'VCB-260805',
                    'note' => 'Đóng học phí đợt đầu',
                    'created_at' => $start,
                ]
            );
        }

        // Student 5: Đóng trọn gói cả khóa
        if (isset($students[4])) {
            $s5 = $students[4];
            $s5->update([
                'billing_type' => 'full_course',
                'monthly_tuition_fee' => 2000000,
                'paid_until' => null,
            ]);
            TuitionPayment::firstOrCreate(
                ['user_id' => $s5->id, 'transaction_code' => 'TRON-GOI-01'],
                [
                    'admin_id' => $admin->id,
                    'amount' => 15000000,
                    'months_count' => 1,
                    'billing_type' => 'full_course',
                    'start_date' => now()->subMonths(2)->toDateString(),
                    'end_date' => null,
                    'payment_method' => 'bank_transfer',
                    'transaction_code' => 'TRON-GOI-01',
                    'note' => 'Thanh toán trọn gói toàn bộ khóa học Price Action Pro',
                    'created_at' => now()->subMonths(2),
                ]
            );
        }
    }
}

