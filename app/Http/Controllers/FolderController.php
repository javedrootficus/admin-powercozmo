<?php
namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index()
    {
  // Get folders for the authenticated user
    $folders = Folder::where('user_id', Auth::id())->get();

    // Return structured JSON response
    return response()->json([
        'success' => true,
        'message' => 'Folders retrieved successfully',
        'data' => $folders
    ], 200);
    }

    public function create(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create the folder
        $folder = Folder::create([
            'user_id' => Auth::id(),
            'name' => $validatedData['name']
        ]);

        // Return response with folder data
        return response()->json([
            'success' => true,
            'message' => 'Folder created successfully',
            'data' => $folder
        ], 201);
    } 

    public function rename(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['name' => 'required|string|max:255']);

        $folder->update(['name' => $request->name]);

         // Return response with folder data
         return response()->json([
            'success' => true,
            'message' => 'Folder updated successfully',
            'data' => $folder
        ], 201);
    }

    public function delete(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $folder->delete();
        return response()->json(['success' => true,
            'message' => 'Folder deleted successfully',
            ]);
    }
}
