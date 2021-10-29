<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Http\Requests\AppealPostRequest;
use App\Models\Appeal;
use App\Rules\AgeRule;
use App\Rules\PhoneRule;
use App\Sanitizers\PhoneSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppealController extends Controller
{
    public function __invoke(Request $request)
    {
        $success = $request->session()->get('success', false);

        if ($request->isMethod('post')) {
            $validated = $request->validate(AppealPostRequest::rules());

            $appeal = new Appeal();
            $appeal->name = $validated['name'];
            $appeal->surname = $validated['surname'];
            $appeal->patronymic = $validated['patronymic'];
            $appeal->age = $validated['age'];
            $appeal->gender = $validated['gender'];
            $appeal->phone = PhoneSanitizer::sanitize($validated['phone']);
            $appeal->email = $validated['email'];
            $appeal->message = $validated['message'];
            $appeal->save();

            $success = true;

            return redirect()
                ->route('appeal')
                ->with('success', $success);
        } else
            return view('appeal', ['success' => $success]);
    }
}
