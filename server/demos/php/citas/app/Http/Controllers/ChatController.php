<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a list of the doctor's active conversations with patients.
     */
    public function index()
    {
        $doctorId = auth()->id();

        // Get the latest message for each patient the doctor has chatted with
        $conversations = Message::where('sender_id', $doctorId)
            ->orWhere('receiver_id', $doctorId)
            ->select('sender_id', 'receiver_id', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('sender_id', 'receiver_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Extract unique patient user IDs
        $patientUserIds = $conversations->map(function ($msg) use ($doctorId) {
            return $msg->sender_id === $doctorId ? $msg->receiver_id : $msg->sender_id;
        })->unique();

        // Fetch the patient User models
        $patients = collect();
        if ($patientUserIds->isNotEmpty()) {
            $patients = User::whereIn('id', $patientUserIds)->whereHas('patientProfile')->get();
        }

        // Also fetch patients this doctor has had appointments with to start new chats
        $doctorProfile = auth()->user()->doctor;
        $appointmentPatients = collect();
        if ($doctorProfile) {
            $appointmentPatients = $doctorProfile->appointments()->with('patient.user')->get()
                ->pluck('patient.user')
                ->filter() // remove nulls
                ->unique('id');
        }

        // Merge both lists
        $availablePatients = $patients->merge($appointmentPatients)->unique('id');

        return view('chat.index', compact('availablePatients'));
    }

    /**
     * Display the chat with a specific patient.
     */
    public function show(User $patient)
    {
        // Only allow chatting with valid patients
        if (!$patient->patientProfile) {
            return redirect()->route('chat.index')->with('error', 'Usuario no válido para chat.');
        }

        $doctorId = auth()->id();

        // Mark unread messages from this patient as read
        Message::where('sender_id', $patient->id)
            ->where('receiver_id', $doctorId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Fetch message history
        $messages = Message::between($doctorId, $patient->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', compact('patient', 'messages'));
    }

    /**
     * Store a new message sent to a patient.
     */
    public function store(Request $request, User $patient)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        if (!$patient->patientProfile) {
            return redirect()->route('chat.index')->with('error', 'Usuario no válido.');
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $patient->id,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('chat.show', $patient);
    }
}
