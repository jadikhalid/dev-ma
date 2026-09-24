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
            // Tests / seeders: accès actif par défaut (grandfather), sauf état trial()/expired().
            'is_subscribed' => true,
            'subscription_expires_at' => null,
            'trial_ends_at' => null,
        ];
    }

    public function onTrial(?\DateTimeInterface $endsAt = null): static
    {
        return $this->state(fn () => [
            'is_subscribed' => false,
            'subscription_expires_at' => null,
            'trial_ends_at' => $endsAt ?? now()->addMonths(CompanyProfile::TRIAL_MONTHS),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'is_subscribed' => false,
            'subscription_expires_at' => now()->subDay(),
            'trial_ends_at' => now()->subDay(),
        ]);
    }
}
