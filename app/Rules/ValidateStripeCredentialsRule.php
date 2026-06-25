<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateStripeCredentialsRule extends AbstractStringRule
{
    /**
     * The authentication error returned by Stripe.
     */
    private ?string $message = null;

    /**
     * Determine if the Stripe secret key authenticates successfully.
     */
    public function passes(string $attribute, string $value): bool
    {
        try {
            \Stripe\Stripe::setApiKey($value);

            \Stripe\Token::retrieve(
                'validate_credentials'
            );
        } catch (\Stripe\Exception\AuthenticationException $e) {
            $this->message = $e->getMessage();

            return false;
        } catch (\Exception $e) {
            return true;
        }

        return true;
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return $this->message ?? __('Invalid Stripe credentials.');
    }
}
