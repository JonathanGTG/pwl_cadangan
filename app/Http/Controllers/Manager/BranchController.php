<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withTrashed()->with('users')->latest()->get();
        return view('manager.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('manager.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'address'        => 'required|string',
            'phone'          => 'nullable|string|max:20',
            'status'         => 'required|in:active,inactive',
            // Data admin cabang
            'admin_name'     => 'required|string|max:100',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        // 1. Buat cabang
        $branch = Branch::create([
            'name'    => $request->name,
            'address' => $request->address,
            'phone'   => $request->phone,
            'status'  => $request->status,
        ]);

        // 2. Buat akun admin cabang otomatis
        User::create([
            'name'      => $request->admin_name,
            'email'     => $request->admin_email,
            'password'  => Hash::make($request->admin_password),
            'role'      => 'admin',
            'branch_id' => $branch->id,
        ]);

        return redirect()->route('manager.branches.index')
                         ->with('success', "Cabang {$branch->name} dan akun admin berhasil dibuat!");
    }

    public function edit(Branch $branch)
    {
        $admins = User::where('branch_id', $branch->id)->where('role', 'admin')->get();
        $kasirs = User::where('branch_id', $branch->id)->where('role', 'kasir')->get();
        return view('manager.branches.edit', compact('branch', 'admins', 'kasirs'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'address' => 'required|string',
            'phone'   => 'nullable|string|max:20',
            'status'  => 'required|in:active,inactive',
        ]);

        $branch->update($request->only('name', 'address', 'phone', 'status'));

        return redirect()->route('manager.branches.index')
                         ->with('success', 'Cabang berhasil diperbarui!');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('manager.branches.index')
                         ->with('success', 'Cabang berhasil dinonaktifkan!');
    }

    public function restore($id)
    {
        Branch::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('manager.branches.index')
                         ->with('success', 'Cabang berhasil dipulihkan!');
    }

    // Tambah kasir untuk cabang
    public function addKasir(Request $request, Branch $branch)
    {
        $request->validate([
            'kasir_name'     => 'required|string|max:100',
            'kasir_email'    => 'required|email|unique:users,email',
            'kasir_password' => 'required|string|min:8',
        ]);

        User::create([
            'name'      => $request->kasir_name,
            'email'     => $request->kasir_email,
            'password'  => Hash::make($request->kasir_password),
            'role'      => 'kasir',
            'branch_id' => $branch->id,
        ]);

        return back()->with('success', 'Akun kasir berhasil ditambahkan!');
    }
}