<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  /**
   * Display a listing of users.
   *
   * @return View
   */


  public function index(): View
  {
    $users = User::with(['role', 'profile'])->get();

    return view('users.index', compact('users'));
  }

  /**
   * Display the specified user.
   *
   * @param User $user
   * @return View
   */
  public function show(User $user): View
  {
    // Load the profile data along with the avatar path
    $user->load('profile');

    return view('users.show', compact('user'));  // Use 'user.show' for view
  }

  /**
   * Show the form for editing the specified user.
   *
   * @param User $user
   * @return View
   */
  public function edit(User $user): View
  {
    // Get all roles for the role selection dropdown
    $roles = Role::all();
    $user->load('profile');  // Load the profile for editing, including avatar

    return view('users.edit', compact('user', 'roles'));  // Use 'user.edit' for view
  }

  /**
   * Update the specified user in storage.
   *
   * @param Request $request
   * @param User $user
   * @return RedirectResponse
   */
  public function update(Request $request, User $user): RedirectResponse
  {
    // Update the user data
    $user->update([
      'name' => $request->name,
      'email' => $request->email,
      'password' => $request->password ? Hash::make($request->password) : $user->password,  // Only update password if provided
      'role_id' => $request->role_id,
    ]);

    // Handle avatar upload if provided
    $avatarPath = $user->profile->avatar;  // Keep existing avatar by default
    if ($request->hasFile('avatar')) {
      // Delete the old avatar from storage if it exists
      if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
        Storage::disk('public')->delete($avatarPath);
      }

      // Store the new avatar and get the path
      $avatarPath = $request->file('avatar')->store('avatars', 'public');
    }

    // Update the profile if provided
    $user->profile()->update([
      'biodata' => $request->biodata,
      'age' => $request->age,
      'address' => $request->address,
      'avatar' => $avatarPath,  // Update avatar path
    ]);

    // Redirect back with a success message
    return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui!');
  }

  /**
   * Store a newly created user in storage.
   *
   * @param Request $request
   * @return RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role_id' => $request->role_id,
    ]);

    $avatarPath = $request->hasFile('avatar')
      ? Storage::disk('public')->put('avatars', $request->file('avatar'))
      : null;

    $user->profile()->create([
      'biodata' => $request->biodata,
      'age' => $request->age,
      'address' => $request->address,
      'avatar' => $avatarPath,
    ]);

    return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan!');
  }


  /**
   * Show the form for creating a new user.
   *
   * @return View
   */
  public function create(): View
  {
    // Get all roles to populate the role dropdown
    $roles = Role::all();
    return view('users.create', compact('roles'));  // Use 'user.create' for view
  }

  /**
   * Remove the specified user from storage.
   *
   * @param User $user
   * @return RedirectResponse
   */
  public function destroy(User $user): RedirectResponse
  {
    // Delete the user's avatar if exists
    $avatarPath = $user->profile->avatar;
    if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
      Storage::disk('public')->delete($avatarPath);
    }

    // Delete the user's profile
    $user->profile()->delete();

    // Delete the user
    $user->delete();

    // Redirect to the user list with a success message
    return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus!');
  }
}
