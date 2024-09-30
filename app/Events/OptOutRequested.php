<?php

namespace App\Events;

use App\Data\SmsCommand;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Thunk\Verbs\Event;

class OptOutRequested extends Event
{
    public static function webhook(Request $request, SmsCommand $command): string
    {
        static::commit(
            phone_number: $request->input('From'),
            payload: $request->all(),
        );

        return implode(' ', [
            'Your phone number and any updates you have sent will be',
            'removed from disastercheckin.com shortly.',
        ]);
    }

    public function __construct(
        public string $phone_number,
        public array $payload,
    ) {}

    public function handle()
    {
        if ($phone_number = PhoneNumber::findByValue($this->phone_number)) {
            $phone_number->check_ins()->delete();
            $phone_number->delete();
        }

        return true;
    }
}
