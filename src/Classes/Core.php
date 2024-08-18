<?php

namespace Upsoftware\Core\Classes;

use Upsoftware\Auth\Classes\Otp;
use Upsoftware\Auth\Enums\OtpKind;

class Core
{
    /**
     * The UpsoftwareCore version.
     *
     * @var string
     */
    const UPSOFTWARE_VERSION = '1.0.0';

    /**
     * Get the version number of the UpsoftwareCore.
     *
     * @return string
     */
    public function version()
    {
        return static::UPSOFTWARE_VERSION;
    }

    public function otp() {
        if (class_exists(Otp::class)) {
            return new Otp();
        } else {
            throw new \Exception('Class Otp is not available. Install Auth package.');
        }
    }
}
