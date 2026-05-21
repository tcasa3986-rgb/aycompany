<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a list of the patient's active conversations.
     */
    public function index()
    {
        $patientUserId = auth()->id();

        // Get the latest message for each doctor the patient has chatted with
        $conversations = Message::where('sender_id', $patientUserId)
            ->orWhere('receiver_id', $patientUserId)
            ->select('sender_id', 'receiver_id', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('sender_id', 'receiver_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Extract unique doctor user IDs
        $doctorIds = $conversations->map(function ($msg) use ($patientUserId) {
            return $msg->sender_id === $patientUserId ? $msg->receiver_id : $msg->sender_id;
        })->unique();

        // Fetch the doctor User models
        $doctors = collect();
        if ($doctorIds->isNotEmpty()) {
            // We get the users that have the 'doctor' role
            $doctors = User::whereIn('id', $doctorIds)->role('doctor')->get();
        }

        // Also fetch doctors this patient has had appointments with to start new chats
        $patient = auth()->user()->patientProfile;
        $appointmentDoctors = collect();
        if ($patient) {
            $appointmentDoctors = $patient->appointments()->with('doctor.user')->get()
                ->pluck('doctor.user')
                ->filter() // remove nulls
                ->unique('id');
        }

        // Merge both lists so the patient can see active chats or start new ones with past doctors
        $availableDoctors = $doctors->merge($appointmentDoctors)->unique('id');

        return view('portal.chat.index', compact('availableDoctors'));
    }

    /**
     * Display the chat with a specific doctor.
     */
    public function show(User $doctor)
    {
        // Only allow chatting with valid doctors
        if (!$doctor->hasRole('doctor')) {
            return redirect()->route('portal.chat.index')->with('error', 'Usuario no válido para chat.');
        }

        $patientUserId = auth()->id();

        // Mark unread messages from this doctor as read
        Message::where('sender_id', $doctor->id)
            ->where('receiver_id', $patientUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Fetch message history
        $messages = Message::between($patientUserId, $doctor->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('portal.chat.show', compact('doctor', 'messages'));
    }

    /**
     * Store a new message sent to a doctor.
     */
    public function store(Request $request, User $doctor)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        if (!$doctor->hasRole('doctor')) {
            return redirect()->route('portal.chat.index')->with('error', 'Usuario no válido.');
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $doctor->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('portal.chat.show', $doctor);
    }
}
