<?php

namespace Modules\Core\Rules;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

use Exception;
use Illuminate\Validation\ValidationException;

class PhoneValidationRule implements Rule
{
    
    /**
     * Public variables
     */
    public $phoneUtil;

    public $message = 'Incorrect phone number format (E164)';


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    } //Function ends


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            //Use the phone util to parse the input string
            $objPhoneNumber = $this->phoneUtil->parse($value);
            if (empty($objPhoneNumber)) { return false; }

            $strPhoneNumberE164 = $this->phoneUtil->format($objPhoneNumber, \libphonenumber\PhoneNumberFormat::E164);
            if (empty($strPhoneNumberE164)) { return false; }

            if ($this->phoneUtil->isValidNumber($objPhoneNumber)) {
                return $strPhoneNumberE164 === $value;
            } else {
                return false;
            } //End if
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        } //End if
    } //Function ends

    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    } //Function ends

} //Class ends
