<?php

declare(strict_types=1);

namespace App\Domain\Customer\Enums;

/**
 * Enumeration of customer account statuses.
 *
 * Represents the various states a customer account can be in
 * throughout its lifecycle in the system.
 */
enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Pending = 'pending';

    /**
     * Get the human-readable label for the status.
     *
     * @return string The display label for UI presentation
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Pending => 'Pending Verification',
        };
    }

    /**
     * Get the color associated with the status.
     *
     * @return string The color name for UI styling
     */
    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Inactive => 'gray',
            self::Suspended => 'red',
            self::Pending => 'yellow',
        };
    }

    /**
     * Get all possible status values as an array.
     *
     * @return array<int, string> Array of status string values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
