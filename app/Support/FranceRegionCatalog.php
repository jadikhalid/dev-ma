<?php

namespace App\Support;

final class FranceRegionCatalog
{
    /**
     * Grandes villes conservées en plus des régions (France).
     *
     * @return list<string>
     */
    public static function majorCities(): array
    {
        return [
            'Paris',
            'Lyon',
            'Marseille',
            'Toulouse',
            'Nice',
            'Nantes',
            'Strasbourg',
            'Montpellier',
            'Bordeaux',
            'Lille',
        ];
    }

    /**
     * @return list<string>
     */
    public static function labels(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $regions = $locale === 'en' ? self::english() : self::french();

        sort($regions, SORT_LOCALE_STRING);

        return $regions;
    }

    /**
     * Anciennes villes hors catalogue → région métropolitaine.
     *
     * @return array<string, string>
     */
    public static function legacyCityMap(): array
    {
        return [
            'Belfort' => 'Bourgogne-Franche-Comté',
            'Besançon' => 'Bourgogne-Franche-Comté',
            'Mulhouse' => 'Grand Est',
            'Metz' => 'Grand Est',
            'Nancy' => 'Grand Est',
            'Reims' => 'Grand Est',
            'Dijon' => 'Bourgogne-Franche-Comté',
            'Rennes' => 'Bretagne',
            'Brest' => 'Bretagne',
            'Tours' => 'Centre-Val de Loire',
            'Orléans' => 'Centre-Val de Loire',
            'Ajaccio' => 'Corse',
            'Bastia' => 'Corse',
            'Rouen' => 'Normandie',
            'Caen' => 'Normandie',
            'Le Havre' => 'Normandie',
            'Clermont-Ferrand' => 'Auvergne-Rhône-Alpes',
            'Grenoble' => 'Auvergne-Rhône-Alpes',
            'Saint-Étienne' => 'Auvergne-Rhône-Alpes',
            'Angers' => 'Pays de la Loire',
            'Le Mans' => 'Pays de la Loire',
            'Poitiers' => 'Nouvelle-Aquitaine',
            'Limoges' => 'Nouvelle-Aquitaine',
            'Perpignan' => 'Occitanie',
            'Nîmes' => 'Occitanie',
            'Amiens' => 'Hauts-de-France',
            'Valenciennes' => 'Hauts-de-France',
        ];
    }

    /**
     * @return list<string>
     */
    private static function french(): array
    {
        return [
            'Auvergne-Rhône-Alpes',
            'Bourgogne-Franche-Comté',
            'Bretagne',
            'Centre-Val de Loire',
            'Corse',
            'Grand Est',
            'Guadeloupe',
            'Guyane',
            'Hauts-de-France',
            'Île-de-France',
            'La Réunion',
            'Martinique',
            'Mayotte',
            'Normandie',
            'Nouvelle-Aquitaine',
            'Occitanie',
            'Pays de la Loire',
            'Provence-Alpes-Côte d\'Azur',
        ];
    }

    /**
     * @return list<string>
     */
    private static function english(): array
    {
        return [
            'Auvergne-Rhône-Alpes',
            'Burgundy-Franche-Comté',
            'Brittany',
            'Centre-Loire Valley',
            'Corsica',
            'Grand Est',
            'Guadeloupe',
            'French Guiana',
            'Hauts-de-France',
            'Île-de-France',
            'Réunion',
            'Martinique',
            'Mayotte',
            'Normandy',
            'New Aquitaine',
            'Occitanie',
            'Pays de la Loire',
            'Provence-Alpes-Côte d\'Azur',
        ];
    }
}
