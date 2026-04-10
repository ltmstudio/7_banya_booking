<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $entity_type
 * @property int $entity_id
 * @property string $action
 * @property string|null $field
 * @property string|null $old_value
 * @property string|null $new_value
 * @property string|null $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forEntity(string $type, int $id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booking_id
 * @property int $extra_service_id
 * @property int $quantity
 * @property numeric $price_at_booking
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereExtraServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService wherePriceAtBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingExtraService whereUpdatedAt($value)
 */
	class BookingExtraService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client todayBirthday()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withoutTrashed()
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string|null $icon_path
 * @property array<array-key, mixed>|null $description
 * @property numeric $price
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereIconPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraService whereUpdatedAt($value)
 */
	class ExtraService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property array<array-key, mixed> $title
 * @property array<array-key, mixed> $body
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PopupPromo whereUpdatedAt($value)
 */
	class PopupPromo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $category
 * @property array<array-key, mixed>|null $description
 * @property numeric $price_per_hour
 * @property numeric|null $promo_price_per_hour
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property numeric|null $child_price_per_hour
 * @property int|null $cleaning_buffer_minutes
 * @property int|null $capacity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoomPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereChildPricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCleaningBufferMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePromoPricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room withoutTrashed()
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $room_id
 * @property string $path
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomPhoto whereUpdatedAt($value)
 */
	class RoomPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin \Illuminate\Database\Eloquent\Builder<static>
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

