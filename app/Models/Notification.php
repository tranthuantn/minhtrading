<?php

namespace App\Models;

/**
 * Class Notification
 *
 * Alias/subclass of UserNotification to provide full compatibility whether
 * referenced as Notification or UserNotification.
 */
class Notification extends UserNotification
{
    // Inherits all attributes, casts, relationships, and scopes from UserNotification
}

