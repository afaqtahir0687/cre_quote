<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('id', 'desc')->paginate(15);
        return view('super_admin.cms.messages.index', compact('messages'));
    }

    public function markRead(ContactMessage $message)
    {
        $message->is_read = true;
        $message->save();

        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->back()->with('success', 'Message deleted.');
    }
}

