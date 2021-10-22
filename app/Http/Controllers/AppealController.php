<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    private $max_length_input = [
        'name' => 20,
        'phone' => 11,
        'email' => 100,
        'message' => 100
    ];

    public function __invoke(Request $request)
    {
        $errors = [];
        $success = $request->session()->get('success', false);

        if ($request->isMethod('post')) {
            $name = $request->input('name');
            $phone = $request->input('phone');
            $email = $request->input('email');
            $message = $request->input('message');

            if ($name === null) {
                $errors['name'] = 'Пустое поле Имя';
            } else if (strlen($name) > $this->max_length_input['name']) {
                $errors['name'] = 'Имя должно быть не длиннее 20 символов';
            }

            if ($message === null) {
                $errors['message'] = 'Пустое сообщение';
            } else if (strlen($message) > $this->max_length_input['message']) {
                $errors['message'] = 'Сообщение должно быть не длинее 100 символов';
            }

            if ($phone === null && $email === null) {
                $errors['email'] = 'Хотя бы одно из полей: Телефон, Почта, должно быть заполнено';
            } else {
                if ($phone !== null && strlen($phone) > $this->max_length_input['phone'])
                    $errors['phone'] = 'Телефон должен быть не длиннее 11 символов';
                if ($email !== null && strlen($email) > $this->max_length_input['email'])
                    $errors['phone'] = 'Почта должна быть не длиннее 100 символов';
            }


            if (count($errors) > 0) {
                $request->flash();
            } else {
                $appeal = new Appeal();
                $appeal->name = $name;
                $appeal->phone = $phone;
                $appeal->email = $email;
                $appeal->message = $message;
                $appeal->save();

                $success = true;

                return redirect()
                    ->route('appeal')
                    ->with('success', $success);
            }
        }

        return view('appeal', ['errors' => $errors, 'success' => $success]);

    }
}
