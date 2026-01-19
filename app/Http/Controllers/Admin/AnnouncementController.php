<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * AnnouncementController - Menangani pengumuman untuk admin
 */
class AnnouncementController extends Controller
{
    /**
     * List all announcements
     */
    public function index()
    {
        $announcements = Announcement::latest()->get();

        return Inertia::render('Admin/Announcement/Index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'] ?? true,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update announcement
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Delete announcement
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        return back()->with('success', 'Status pengumuman berhasil diperbarui.');
    }
}
