<?php
namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index()
    {
        return response()->json(Folder::where('user_id', Auth::id())->get());
    }

    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Folder created', 'folder' => $folder], 201);
    }

    public function rename(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['name' => 'required|string|max:255']);

        $folder->update(['name' => $request->name]);

        return response()->json(['message' => 'Folder renamed', 'folder' => $folder]);
    }

    public function delete(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->delete();
        return response()->json(['message' => 'Folder deleted']);
    }
}
