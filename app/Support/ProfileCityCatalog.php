<?php

namespace App\Support;

use App\Models\Profile;

final class ProfileCityCatalog
{
    /**
     * @return list<string>
     */
    public static function forCountry(string $country, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $cities = match ($country) {
            Profile::COUNTRY_MA => self::morocco($locale),
            Profile::COUNTRY_FR => self::france($locale),
            Profile::COUNTRY_ES => self::spain($locale),
            Profile::COUNTRY_BE => self::belgium($locale),
            Profile::COUNTRY_DE => self::germany($locale),
            Profile::COUNTRY_CA => self::canada($locale),
            Profile::COUNTRY_OTHER => self::other($locale),
            default => [],
        };

        sort($cities, SORT_LOCALE_STRING);

        return $cities;
    }

    /**
     * @return list<string>
     */
    private static function morocco(string $locale): array
    {
        return $locale === 'en'
            ? ['Agadir', 'Casablanca', 'Fes', 'Kenitra', 'Marrakech', 'Meknes', 'Oujda', 'Rabat', 'Tangier', 'Tetouan']
            : ['Agadir', 'Casablanca', 'Fès', 'Kénitra', 'Marrakech', 'Meknès', 'Oujda', 'Rabat', 'Tanger', 'Tétouan'];
    }

    /**
     * @return list<string>
     */
    private static function france(string $locale): array
    {
        return ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille'];
    }

    /**
     * @return list<string>
     */
    private static function spain(string $locale): array
    {
        return $locale === 'en'
            ? ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Bilbao', 'Malaga', 'Zaragoza', 'Murcia', 'Palma', 'Alicante']
            : ['Madrid', 'Barcelone', 'Valence', 'Séville', 'Bilbao', 'Malaga', 'Saragosse', 'Murcie', 'Palma', 'Alicante'];
    }

    /**
     * @return list<string>
     */
    private static function belgium(string $locale): array
    {
        return $locale === 'en'
            ? ['Brussels', 'Antwerp', 'Ghent', 'Liège', 'Charleroi', 'Bruges', 'Namur', 'Leuven', 'Mons', 'Mechelen']
            : ['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Charleroi', 'Bruges', 'Namur', 'Louvain', 'Mons', 'Malines'];
    }

    /**
     * @return list<string>
     */
    private static function germany(string $locale): array
    {
        return $locale === 'en'
            ? ['Berlin', 'Munich', 'Hamburg', 'Frankfurt', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Leipzig', 'Dortmund', 'Essen']
            : ['Berlin', 'Munich', 'Hambourg', 'Francfort', 'Cologne', 'Stuttgart', 'Düsseldorf', 'Leipzig', 'Dortmund', 'Essen'];
    }

    /**
     * @return list<string>
     */
    private static function canada(string $locale): array
    {
        return $locale === 'en'
            ? ['Toronto', 'Montreal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Winnipeg', 'Quebec City', 'Hamilton', 'Halifax']
            : ['Toronto', 'Montréal', 'Vancouver', 'Calgary', 'Ottawa', 'Edmonton', 'Winnipeg', 'Québec', 'Hamilton', 'Halifax'];
    }

    /**
     * @return list<string>
     */
    private static function other(string $locale): array
    {
        return $locale === 'en'
            ? ['London', 'Dubai', 'Geneva', 'Amsterdam', 'Zurich', 'Singapore', 'Hong Kong', 'Sydney', 'Tokyo', 'Other']
            : ['Londres', 'Dubaï', 'Genève', 'Amsterdam', 'Zurich', 'Singapour', 'Hong Kong', 'Sydney', 'Tokyo', 'Autre'];
    }
}
