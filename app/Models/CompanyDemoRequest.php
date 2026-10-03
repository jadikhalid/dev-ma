<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

#[Fillable([
    'company_name',
    'company_size',
    'contact_name',
    'first_name',
    'last_name',
    'email',
    'phone',
    'hires_planned',
    'hiring_locations',
    'hiring_city',
    'uses_ats',
    'preferred_date',
    'preferred_slots',
    'meeting_platform',
    'message',
    'status',
    'handled_by',
    'handled_at',
    'locale',
    'ip_address',
])]
class CompanyDemoRequest extends Model
{
    public const STATUS_NEW = 'new';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_DONE = 'done';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_SCHEDULED,
        self::STATUS_DONE,
    ];

    public const COMPANY_SIZES = ['1-10', '11-50', '51-200', '201-500', '500+'];

    public const HIRES_PLANNED = ['1-4', '5-10', '11-50', '50+'];

    public const HIRING_LOCATIONS = ['ma', 'fr', 'be', 'ch', 'ca', 'es', 'de', 'gb', 'us', 'ae', 'other'];

    public const ATS_OPTIONS = ['yes', 'no', 'unsure'];

    /** Créneaux d'1 h, 9h–17h (début). */
    public const SLOT_STARTS = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];

    public const MEETING_PLATFORMS = ['teams', 'meet', 'zoom'];

    public const BOOKING_WEEKDAYS_AHEAD = 15;

    /** Indicatifs proposés devant le numéro de mobile (clé = code pays ISO). */
    public const PHONE_COUNTRIES = [
        'ma' => ['dial' => '+212', 'flag' => '🇲🇦'],
        'fr' => ['dial' => '+33', 'flag' => '🇫🇷'],
        'be' => ['dial' => '+32', 'flag' => '🇧🇪'],
        'ch' => ['dial' => '+41', 'flag' => '🇨🇭'],
        'ca' => ['dial' => '+1', 'flag' => '🇨🇦'],
        'es' => ['dial' => '+34', 'flag' => '🇪🇸'],
        'de' => ['dial' => '+49', 'flag' => '🇩🇪'],
        'gb' => ['dial' => '+44', 'flag' => '🇬🇧'],
        'us' => ['dial' => '+1', 'flag' => '🇺🇸'],
        'ae' => ['dial' => '+971', 'flag' => '🇦🇪'],
    ];

    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
            'preferred_date' => 'date',
            'hiring_locations' => 'array',
            'preferred_slots' => 'array',
        ];
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Prochains jours ouvrés (lun–ven) proposés pour la démo.
     *
     * @return Collection<int, Carbon>
     */
    public static function availableBookingDates(?Carbon $from = null): Collection
    {
        $cursor = ($from ?? now())->copy()->startOfDay()->addDay();
        $dates = collect();

        while ($dates->count() < self::BOOKING_WEEKDAYS_AHEAD) {
            if ($cursor->isWeekday()) {
                $dates->push($cursor->copy());
            }
            $cursor->addDay();
        }

        return $dates;
    }

    public static function slotLabel(string $start): string
    {
        [$hour, $minute] = array_pad(explode(':', $start), 2, '00');
        $endHour = str_pad((string) (((int) $hour) + 1), 2, '0', STR_PAD_LEFT);

        return $start.' – '.$endHour.':'.$minute;
    }

    /**
     * Réponses de qualification renseignées, sous forme libellé => valeur.
     *
     * @return array<string, string>
     */
    public function needsSummary(): array
    {
        $prefix = 'talenma.company_offer.demo_form.summary.';

        return array_filter([
            __($prefix.'company_size') => $this->company_size
                ? $this->company_size.' '.__('talenma.company_offer.demo_form.company_size_suffix')
                : null,
            __($prefix.'hires_planned') => $this->hires_planned,
            __($prefix.'hiring_locations') => implode(', ', $this->hiringLocationLabels()) ?: null,
            __($prefix.'hiring_city') => $this->hiring_city,
            __($prefix.'uses_ats') => $this->uses_ats
                ? __('talenma.company_offer.demo_form.ats.'.$this->uses_ats)
                : null,
            __($prefix.'preferred_date') => $this->preferred_date?->translatedFormat('l j F Y'),
            __($prefix.'preferred_slots') => $this->preferredSlotLabels() ? implode(', ', $this->preferredSlotLabels()) : null,
            __($prefix.'meeting_platform') => $this->meeting_platform
                ? __('talenma.company_offer.demo_form.platforms.'.$this->meeting_platform)
                : null,
        ], fn ($value) => filled($value));
    }

    /**
     * @return list<string>
     */
    public function hiringLocationLabels(): array
    {
        return collect($this->hiring_locations ?? [])
            ->map(fn (string $code) => __('talenma.company_offer.demo_form.locations.'.$code))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function preferredSlotLabels(): array
    {
        return collect($this->preferred_slots ?? [])
            ->map(fn (string $start) => self::slotLabel($start))
            ->values()
            ->all();
    }
}
