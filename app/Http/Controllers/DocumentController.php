<?php
namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Role;
use App\Models\DocumentVersion;
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

        return response()->json(['success' => true,'message' => 'Document created successfully', 'data' => $document], 201);
    }

    // Get all documents (Viewers, Editors, Admins)
    public function index(Request $request)
    {
        $query = Document::where('user_id', Auth::id());
        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'LIKE', "%$search%")
                  ->orWhere('content', 'LIKE', "%$search%");
        }
       // return response()->json($query->get());
        // Return structured JSON response
    return response()->json([
        'success' => true,
        'message' => 'Document retrieved successfully',
        'data' => $query->get()
    ], 200);
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

        // Save old version
        DocumentVersion::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'content' => $document->content,
        ]);


        $document->update($request->only(['title', 'content']));

        return response()->json(['success' => true,'message' => 'Document updated successfully', 'data' => $document]);
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

        return response()->json(['success' => true,'message' => 'Document deleted successfully']);
    }

    // Get all versions of a document
    public function getVersions($id)
    {
        $versions = DocumentVersion::where('document_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true,'message' => 'Document Version retrieved successfully', 'data' => $versions]);
    }
}
