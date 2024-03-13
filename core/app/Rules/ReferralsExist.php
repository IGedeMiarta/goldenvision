<?php

// File: app/Rules/ReferralsExist.php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ReferralsExist implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!User::where('username', $value)->exists()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'referrals username do not exist.';
    }
}
