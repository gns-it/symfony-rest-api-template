<?php

namespace App\Service\Recaptcha;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Validator
 * @package App\Service\Recaptcha
 */
class Validator
{
    /**
     * Recapcha verify url
     * @var string
     */
    const RECAPCHA_URL = "https://www.google.com/recaptcha/api/siteverify";

    /**
     * @var float
     */
    const DEFAULT_SCORE = 0.5;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Validator constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Validate provided recapcha code
     * @param FormEvent $event
     * @param array $options
     */
    public function validateForm(FormEvent $event, array $options)
    {
        if (getenv('APP_ENV') === 'dev') {
            return;
        }
        /** @var FormEvent $event */
        $form = $event->getForm();
        $code = $form->getData();
        if (!($code !== null && is_string($code))) {
            $form->addError(new FormError($this->translator->trans($options['invalid_message'], array())));
        }
        try {
            if (!$this->isValid($code, $options['min_score'])) {
                $form->addError(new FormError($this->translator->trans($options['invalid_message'], array())));
            }
        } catch (\Exception $exception) {
            $form->addError(new FormError('Recapcha request failed.'));
        }
    }

    /**
     * Validate provided recapcha code
     * @param string $code
     * @param float $score
     * @return bool
     */
    public function isValid(string $code, float $score = self::DEFAULT_SCORE)
    {
        $captcha = file_get_contents(self::RECAPCHA_URL.'?secret='.getenv('RECAPCHA_SECRET').'&response='.$code);
        $captcha = json_decode($captcha);
        if (!$captcha->success || $captcha->score <= $score) {
            return false;
        }

        return true;
    }
}