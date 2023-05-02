<?php

namespace App\Http\Controllers;

use App\Helpers\SMS;
use App\Models\Mokam;
use App\Models\Party;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SMSController extends Controller
{
    protected string $permission_for = 'sms';
    public $sender_id, $api_key;

    public function __construct()
    {
        $this->sender_id = env('SMS_SENDER_ID');
        $this->api_key = env('SMS_API_KEY');
    }

    public function index()
    {
        $this->hasPermission('view');

        $messages = \App\Models\Sms::query();
        if (request()->search) {
            if (request()->from_date) {
                $messages = $messages->whereBetween('created_at', [request()->from_date, request()->to_date]);
            }

            if (request()->number) {
                $messages = $messages->where('number', \request('number'));
            }
        }
        $messages = $messages
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('SMS/Index', [
            'messages' => $messages,
        ]);
    }

    /**
     * group sms view page
     * @return \Inertia\Response
     */
    public function groupSms()
    {
        $this->hasPermission('group_sms');

        $remaining_sms = SMS::remainingSms();
        $send_sms = count(\App\Models\Sms::all());
        $customers = Party::customer()->paginate(request('paginate_number', 30));
        return Inertia::render('SMS/GroupSms', compact(
            'customers',
            'remaining_sms',
            'send_sms',
        ));
    }

    /**
     * group sms send
     * @param Request $request
     */
    public function groupSmsSend(Request $request)
    {
        $this->hasPermission('group_sms');
        $request->validate([
            'message' => 'required|string',
            'selectedPhoneNumbers' => 'required|array',
            'selectedPhoneNumbers.*' => 'string|size:11|starts_with:01'
        ]);

        $message = $request->message . " " . config('sms.regards');
        $numbers = join(',', $request->selectedPhoneNumbers);
        $data = SMS::customSmsSend($this->sender_id, $this->api_key, $numbers, $message); //send sms

        if ($data == '202') {
            foreach ($request->selectedPhoneNumbers as $number) {
                $sms_data = [
                    'number' => $number,
                    'message' => $message,
                    'character' => $request->total_characters,
                    'total_sms' => $request->total_messages,
                    'status' => 'success',
                ];
                \App\Models\Sms::create($sms_data);
            }
        }

        if ($data == '202'){
            return redirect()->route('sms.groupSms')->with('success', 'SMS send successfully');
        }else{
            return response()->json($data);
        }
    }

    /**
     * custom sms view
     * @return \Inertia\Response
     */
    public function customSms()
    {
        $this->hasPermission('custom_sms');

        $remaining_sms = SMS::remainingSms();
        $send_sms = count(\App\Models\Sms::all());
        return Inertia::render('SMS/CustomSms', compact('remaining_sms', 'send_sms'));
    }

    /**
     * custom sms send
     * @param Request $request
     */
    public function customSmsSend(Request $request)
    {
        $this->hasPermission('custom_sms');
//        return $request->all();
        $request->validate([
            'message' => 'required|string',
            'mobiles' => 'required|string',
        ]);

        $message = $request->message . " " . config('sms.regards');
        $data = SMS::customSmsSend($this->sender_id, $this->api_key, $request->mobiles, $message); //send sms
        $mobiles = explode(',', $request->mobiles);

        if ($data == '202') {
            foreach ($mobiles as $number) {
                $sms_data = [
                    'number' => $number,
                    'message' => $message,
                    'character' => $request->total_characters,
                    'total_sms' => $request->total_messages,
                    'status' => 'success',
                ];
                \App\Models\Sms::create($sms_data);
            }
        }

        if ($data == '202'){
            return redirect()->route('sms.customSms')->with('success', 'SMS send successfully');
        }else{
            return response()->json($data);
        }
    }
}
