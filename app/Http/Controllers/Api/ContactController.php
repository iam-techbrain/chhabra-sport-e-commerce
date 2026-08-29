<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $msg = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'company' => $request->company ?? null,
            'message' => $request->message,
            'status' => 'unread'
        ]);

        return response()->json([
            'success' => true,
            'message' => "Thank you {$msg->name}! Your message has been saved and sent to Chhabra Sports team.",
            'data' => $msg
        ], 201);
    }

    public function index()
    {
        $messages = ContactMessage::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'unread_count' => ContactMessage::where('status', 'unread')->count()
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $msg = ContactMessage::find($id);
        if (!$msg) {
            return response()->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        $msg->status = $request->status ?? 'read';
        $msg->save();

        return response()->json([
            'success' => true,
            'message' => "Message status updated to {$msg->status}",
            'data' => $msg
        ]);
    }

    public function destroy($id)
    {
        $msg = ContactMessage::find($id);
        if ($msg) {
            $msg->delete();
            return response()->json(['success' => true, 'message' => 'Contact message deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Message not found'], 404);
    }
}
