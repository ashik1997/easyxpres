<?php

namespace App\Http\Controllers;

use App\Models\MaliOperator;
use Illuminate\Http\Request;

class MaliOperatorController extends Controller
{
    public function index()
    {
        $operators = MaliOperator::get();
        return view('admin.mali-operators.index', compact('operators'));
    }

    public function edit($id)
    {
        $operator = MaliOperator::findOrFail($id);
        return view('admin.mali-operators.edit', compact('operator'));
    }

    public function update(Request $request, $id)
    {
        $operator = MaliOperator::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|numeric|unique:mali_operators,code,' . $id,
            'commission' => 'required|numeric|min:0|max:100',
        ]);
        $operator->update([
            'name' => $request->name,
            'code' => $request->code,
            'commission' => $request->commission,
        ]);
        return redirect()
            ->route('admin.mali.operators.index')
            ->with('success', 'Operator updated successfully');
    }
}
