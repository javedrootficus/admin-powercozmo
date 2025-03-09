<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // Create a new document (Admin & Editor only)
    public function create(Request $request)
    {
        if (!in_array(Auth::user()->role->name, ['admin', 'editor'])) {
            return response()->json(['error' => 'Access Denied !'], 403);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        $document = Document::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'folder_id' => $request->folder_id
        ]);

        return response()->json(['message' => 'Document created successfully', 'document' => $document], 201);
    }

    // Get all documents (Viewers, Editors, Admins)
    public function index()
    {
        $documents = Document::all();
        return response()->json($documents);
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return response()->json($document);
    }

    // Update a document (Only Admin & Document Owner)
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Ensure the user is either an Admin or the owner of the document
        if (Auth::user()->role->name !== 'admin' && Auth::id() !== $document->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        $document->update($request->only(['title', 'content', 'folder_id']));

        return response()->json(['message' => 'Document updated successfully', 'document' => $document]);
    }

    // Delete a document (Admin only)
    public function delete($id)
    {
        $document = Document::findOrFail($id);

        // Only Admins can delete documents
        if (Auth::user()->role->name !== 'admin') {
            return response()->json(['error' => 'Access Denied'], 403);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}
