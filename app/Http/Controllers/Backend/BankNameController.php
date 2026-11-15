<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankNameController extends Controller
{
    public function index()
    {
        $iBanking = BankName::latest()->get();
        return view('backend.ibanking.index', compact('iBanking'));
    }

     public function create()
    {
        // Logic to show form to create a new package
        return view('backend.ibanking.create');
    }

      public function store(Request $request)
    {


        $request->validate([
            'bank_name' => 'required',
        ]);

       

        BankName::create([
            'bank_name' => $request->bank_name,
      
        ]);

        return redirect()
            ->route('admin.ibanking.list')
            ->with('success', 'Mobile Banking created successfully');
    }

     public function edit($id)
    {
        // Logic to show form to edit an existing package
        $iBank = BankName::findOrFail($id);
        return view('backend.ibanking.edit', compact('iBank'));
    }

     public function update(Request $request, $id)
    {
        // Logic to update an existing package
        $iBank = BankName::findOrFail($id);
        $request->validate([
            'bank_name' => 'required',
        ]);
       
        $iBank->bank_name = $request->bank_name;
        $iBank->save();
        return redirect()
            ->route('admin.ibanking.list')
            ->with('success', 'Bank Name updated successfully');
    }


     public function changeStatus($id)
    {
        $iBank = BankName::findOrFail($id);

        // Toggle status correctly for ENUM
        $iBank->status = ($iBank->status === 'active')
            ? 'inactive'
            : 'active';

        $iBank->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

     public function destroy($id)
    {
        // Logic to delete a package
        $iBank = BankName::findOrFail($id);
        $iBank->delete();
        return redirect()
            ->route('admin.ibanking.list')
            ->with('success', 'Bank Name deleted successfully');
    }

    // iBanking Rate
     public function rate()
    {
        $appSetup = DB::table('ibanking_rates')->where('id', 1)->first();

        return view('backend.ibanking.rate', compact('appSetup'));
    }

      public function updateRate(Request $request)
    {
       

        DB::table('ibanking_rates')->updateOrInsert(
            ['id' => 1],
            [
                'rate'        => $request->rate
            ]
        );

        return redirect()
            ->route('admin.ibanking.rate')
            ->with('success', 'iBanking Rate updated successfully.');
    }
}
