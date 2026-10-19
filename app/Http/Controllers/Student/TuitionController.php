<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TuitionController extends Controller
{
    /**
     * Display student's own tuition status and payment receipts history.
     */
    public function index(Request $request): View
    {
        $student = $request->user();
        $payments = $student->tuitionPayments()->with(['admin', 'course'])->paginate(10);
        $totalPaid = $student->tuitionPayments()->sum('amount');
        $statusInfo = $student->tuition_status;

        return view('student.tuition.index', compact('student', 'payments', 'totalPaid', 'statusInfo'));
    }
}

