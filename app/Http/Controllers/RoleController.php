<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  public function index(): Factory|\Illuminate\Foundation\Application|View|Application
  {
    $roles = Role::all();
    return view('roles.index', compact('roles'));
  }

  public function store(Request $request): RedirectResponse
  {
    Role::create([
      'name' => $request->name,
    ]);

    return redirect()->route('roles.index')->with('success', 'Peran berhasil ditambahkan!');
  }

  public function create(): Factory|\Illuminate\Foundation\Application|View|Application
  {
    return view('roles.create');
  }

  public function show(Role $role): Factory|\Illuminate\Foundation\Application|View|Application
  {
    return view('roles.show', compact('role'));
  }

  public function edit(Role $role): Factory|\Illuminate\Foundation\Application|View|Application
  {
    return view('roles.edit', compact('role'));
  }

  public function update(Request $request, Role $role): RedirectResponse
  {
    $role->update([
      'name' => $request->name,
    ]);

    return redirect()->route('roles.index')->with('success', 'Peran berhasil diperbarui!');
  }

  public function destroy(Role $role): RedirectResponse
  {
    $role->delete();

    return redirect()->route('roles.index')->with('success', 'Peran berhasil dihapus!');
  }
}
