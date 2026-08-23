<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Support\ProfileCityCatalog;
use App\Support\UsStateCatalog;
use Tests\TestCase;

class ProfileCityCatalogTest extends TestCase
{
    public function test_morocco_offers_ten_major_cities(): void
    {
        app()->setLocale('fr');

        $cities = Profile::citiesForCountry(Profile::COUNTRY_MA);

        $this->assertCount(10, $cities);
        $this->assertContains('Casablanca', $cities);
    }

    public function test_france_offers_ten_major_cities(): void
    {
        app()->setLocale('fr');

        $cities = Profile::citiesForCountry(Profile::COUNTRY_FR);

        $this->assertCount(10, $cities);
        $this->assertContains('Paris', $cities);
    }

    public function test_other_country_offers_ten_major_cities(): void
    {
        app()->setLocale('fr');

        $cities = Profile::citiesForCountry(Profile::COUNTRY_OTHER);

        $this->assertCount(10, $cities);
        $this->assertContains('Londres', $cities);
    }

    public function test_us_profile_cities_lists_all_fifty_states_in_french(): void
    {
        app()->setLocale('fr');

        $states = Profile::citiesForCountry(Profile::COUNTRY_US);

        $this->assertCount(50, $states);
        $this->assertContains('Californie', $states);
        $this->assertContains('Caroline du Nord', $states);
        $this->assertNotContains('California', $states);
    }

    public function test_us_profile_cities_lists_all_fifty_states_in_english(): void
    {
        app()->setLocale('en');

        $states = Profile::citiesForCountry(Profile::COUNTRY_US);

        $this->assertCount(50, $states);
        $this->assertContains('California', $states);
        $this->assertContains('North Carolina', $states);
    }

    public function test_spain_cities_use_english_labels_when_locale_is_en(): void
    {
        app()->setLocale('en');

        $cities = ProfileCityCatalog::forCountry(Profile::COUNTRY_ES);

        $this->assertContains('Barcelona', $cities);
        $this->assertNotContains('Barcelone', $cities);
    }

    public function test_us_state_catalog_is_sorted_alphabetically(): void
    {
        app()->setLocale('fr');

        $states = UsStateCatalog::labels();
        $sorted = $states;
        sort($sorted, SORT_LOCALE_STRING);

        $this->assertSame($sorted, $states);
    }

    public function test_profile_city_catalog_is_sorted_alphabetically(): void
    {
        app()->setLocale('fr');

        $cities = ProfileCityCatalog::forCountry(Profile::COUNTRY_FR);
        $sorted = $cities;
        sort($sorted, SORT_LOCALE_STRING);

        $this->assertSame($sorted, $cities);
    }
}
