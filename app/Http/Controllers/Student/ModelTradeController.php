<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ModelTrade;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModelTradeController extends Controller
{
    /**
     * Display a listing of published model trades for students.
     */
    public function index(Request $request): View
    {
        $query = ModelTrade::published()->with(['creator', 'sourceTrade.user']);

        if ($request->filled('pair')) {
            $query->where('pair', 'like', '%' . $request->input('pair') . '%');
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        if ($request->filled('setup')) {
            $query->where('setup_name', 'like', '%' . $request->input('setup') . '%');
        }

        $modelTrades = $query->latest()->paginate(15)->withQueryString();

        return view('student.model-trades.index', compact('modelTrades'));
    }

    /**
     * Display the specified published model trade detail.
     */
    public function show(ModelTrade|int $modelTrade): View
    {
        $modelTrade = $modelTrade instanceof ModelTrade
            ? $modelTrade
            : ModelTrade::published()->findOrFail($modelTrade);

        abort_if(! $modelTrade->is_published, 404, 'Lệnh mẫu không tồn tại hoặc chưa được công khai.');

        $modelTrade->load(['creator', 'sourceTrade.user']);

        return view('student.model-trades.show', compact('modelTrade'));
    }
}
