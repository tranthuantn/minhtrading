<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\TradeEntry;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students with search and pagination.
     */
    public function index(Request $request): View
    {
        $query = User::students();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $students = $query->withCount(['tradeEntries', 'submissions'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'student',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Đã thêm học viên mới thành công: ' . $user->name);
    }

    /**
     * Display the specified student profile and performance summary.
     */
    public function show(int $id): View
    {
        $student = User::students()
            ->with(['submissions.assignment', 'tradeEntries' => function ($q) {
                $q->latest()->take(10);
            }])
            ->findOrFail($id);

        $totalTrades = TradeEntry::where('user_id', $student->id)->count();
        $winTrades = TradeEntry::where('user_id', $student->id)->where('status', 'win')->count();
        $lossTrades = TradeEntry::where('user_id', $student->id)->where('status', 'loss')->count();
        $breakevenTrades = TradeEntry::where('user_id', $student->id)->where('status', 'breakeven')->count();
        $completedTrades = $winTrades + $lossTrades;
        $winRate = $completedTrades > 0 ? round(($winTrades / $completedTrades) * 100, 2) : 0;
        $totalPnl = (float) TradeEntry::where('user_id', $student->id)->sum('pnl');

        $submissionsCount = Submission::where('user_id', $student->id)->count();
        $averageScore = Submission::where('user_id', $student->id)
            ->whereNotNull('score')
            ->avg('score');
        $averageScore = $averageScore !== null ? round((float) $averageScore, 1) : 0;

        return view('admin.students.show', compact(
            'student',
            'totalTrades',
            'winTrades',
            'lossTrades',
            'breakevenTrades',
            'winRate',
            'totalPnl',
            'submissionsCount',
            'averageScore'
        ));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(int $id): View
    {
        $student = User::students()->findOrFail($id);

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $student = User::students()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'password' => ['nullable', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        $student->name = $validated['name'];
        $student->email = $validated['email'];
        $student->phone = $validated['phone'] ?? null;
        $student->is_active = (bool) $validated['is_active'];

        if (! empty($validated['password'])) {
            $student->password = Hash::make($validated['password']);
        }

        $student->save();

        return redirect()->route('admin.students.show', $student->id)
            ->with('success', 'Thông tin học viên đã được cập nhật thành công.');
    }

    /**
     * Soft delete the student (set is_active = false).
     */
    public function destroy(int $id): RedirectResponse
    {
        $student = User::students()->findOrFail($id);
        $student->update(['is_active' => false]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Đã vô hiệu hóa tài khoản học viên ' . $student->name . ' thành công.');
    }

    /**
     * Detailed statistics page for a specific student.
     */
    public function stats(Request $request, int $id): View|JsonResponse
    {
        $student = User::students()->findOrFail($id);

        // All trades for statistics
        $trades = TradeEntry::where('user_id', $student->id)
            ->orderBy('entry_time', 'asc')
            ->get();

        $totalTrades = $trades->count();
        $winCount = $trades->where('status', 'win')->count();
        $lossCount = $trades->where('status', 'loss')->count();
        $breakevenCount = $trades->where('status', 'breakeven')->count();
        $openCount = $trades->where('status', 'open')->count();
        $closedTrades = $winCount + $lossCount;
        $winRate = $closedTrades > 0 ? round(($winCount / $closedTrades) * 100, 2) : 0;
        $totalPnl = (float) $trades->sum('pnl');

        // Profit Factor
        $grossProfit = (float) $trades->where('pnl', '>', 0)->sum('pnl');
        $grossLoss = abs((float) $trades->where('pnl', '<', 0)->sum('pnl'));
        $profitFactor = $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : ($grossProfit > 0 ? 999 : 0);

        // Cumulative P&L chart data
        $cumulativePnl = 0;
        $pnlChartLabels = [];
        $pnlChartData = [];
        foreach ($trades as $index => $trade) {
            $cumulativePnl += (float) ($trade->pnl ?? 0);
            $dateLabel = $trade->entry_time ? $trade->entry_time->format('d/m/Y') : '#' . ($index + 1);
            $pnlChartLabels[] = $dateLabel;
            $pnlChartData[] = round($cumulativePnl, 2);
        }

        // Pair distribution
        $pairStats = $trades->groupBy('pair')->map(function ($items, $pair) {
            $pairWins = $items->where('status', 'win')->count();
            $pairLosses = $items->where('status', 'loss')->count();
            $pairTotal = $items->count();
            $pairWinRate = ($pairWins + $pairLosses) > 0 ? round(($pairWins / ($pairWins + $pairLosses)) * 100, 1) : 0;

            return [
                'pair' => $pair,
                'total' => $pairTotal,
                'wins' => $pairWins,
                'losses' => $pairLosses,
                'pnl' => round((float) $items->sum('pnl'), 2),
                'win_rate' => $pairWinRate,
            ];
        })->sortByDesc('total')->values();

        // Assignment scores
        $submissions = Submission::with('assignment')
            ->where('user_id', $student->id)
            ->whereNotNull('score')
            ->latest('submitted_at')
            ->get();

        $assignmentLabels = $submissions->pluck('assignment.title')->take(10)->reverse()->values();
        $assignmentScores = $submissions->pluck('score')->take(10)->reverse()->values();

        $statsData = [
            'summary' => [
                'total_trades' => $totalTrades,
                'win_count' => $winCount,
                'loss_count' => $lossCount,
                'breakeven_count' => $breakevenCount,
                'open_count' => $openCount,
                'win_rate' => $winRate,
                'total_pnl' => $totalPnl,
                'profit_factor' => $profitFactor,
            ],
            'pnl_chart' => [
                'labels' => $pnlChartLabels,
                'datasets' => [
                    [
                        'label' => 'Đường cong vốn (P&L $)',
                        'data' => $pnlChartData,
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'win_loss_chart' => [
                'labels' => ['Thắng', 'Thua', 'Hòa'],
                'datasets' => [
                    [
                        'data' => [$winCount, $lossCount, $breakevenCount],
                        'backgroundColor' => ['#10B981', '#EF4444', '#6B7280'],
                    ],
                ],
            ],
            'pair_stats' => $pairStats,
            'assignment_chart' => [
                'labels' => $assignmentLabels,
                'datasets' => [
                    [
                        'label' => 'Điểm số bài tập',
                        'data' => $assignmentScores,
                        'backgroundColor' => '#3B82F6',
                    ],
                ],
            ],
        ];

        if ($request->wantsJson()) {
            return response()->json($statsData);
        }

        $tradeHistory = TradeEntry::where('user_id', $student->id)
            ->latest('entry_time')
            ->paginate(15);

        return view('admin.students.stats', compact('student', 'statsData', 'tradeHistory'));
    }
}
