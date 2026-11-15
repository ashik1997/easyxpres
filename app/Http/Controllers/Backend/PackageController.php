<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('backend.packages.index', compact('packages'));
    }

    public function create()
    {
        // Logic to show form to create a new package
        return view('backend.packages.create');
    }

    public function store(Request $request)
    {

     
        $request->validate([
            'name' => 'required|string|max:255',
            'image_icon' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image_icon')) {
            $imagePath = $request->file('image_icon')->store('packages', 'public');
        }

        Package::create([
            'name' => $request->name,
            'image_icon' => $imagePath,
        ]);

        return redirect()
            ->route('admin.packages.list')
            ->with('success', 'Package created successfully');
    }


    public function edit($id)
    {
        // Logic to show form to edit an existing package
        $package = Package::findOrFail($id);
        return view('backend.packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing package
        $package = Package::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'image_icon' => 'nullable|image|mimes:webp,svg,png,jpg,jpeg|max:2048',
        ]);
        if ($request->hasFile('image_icon')) {
            $imagePath = $request->file('image_icon')->store('packages', 'public');
            $package->image_icon = $imagePath;
        }
        $package->name = $request->name;
        $package->save();
        return redirect()
            ->route('admin.packages.list')
            ->with('success', 'Package updated successfully');
    }

    public function destroy($id)
    {
        // Logic to delete a package
        $package = Package::findOrFail($id);
        $package->delete();
        return redirect()
            ->route('admin.packages.list')
            ->with('success', 'Package deleted successfully');
    }

    // status change method
    public function changeStatus($id)
{
    $package = Package::findOrFail($id);

    // Toggle status correctly for ENUM
    $package->status = ($package->status === 'active')
        ? 'inactive'
        : 'active';

    $package->save();

    return redirect()->back()->with('success', 'Status updated successfully.');
}

}
