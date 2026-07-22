<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompanyProfile> */
class CompanyProfileFactory extends Factory
{
    public function definition(): array
    {
        $sectors = ['SaaS', 'E-commerce', 'Fintech', 'Agence digitale', 'Industrie', 'EdTech'];
        $country = $this->faker->randomElement(CompanyProfile::COUNTRY_CODES);

        return [
            'sector' => $this->faker->randomElement($sectors),
            'country' => $country,
            'city' => $this->faker->randomElement(CompanyProfile::citiesForCountry($country) ?: ['Paris']),
            'description' => 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.',
            'website' => $this->faker->url(),
            'employee_count' => $this->faker->randomElement(['1-10', '11-50', '51-200', '200+']),
            'hiring_needs' => 'Talents full-stack, mobile et backend pour missions longue durée.',
        ];
    }
}
