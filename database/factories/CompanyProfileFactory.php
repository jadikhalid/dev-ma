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
        $cities = ['Paris', 'Lyon', 'Bordeaux', 'Nantes', 'Bruxelles', 'Genève'];

        return [
            'company_name' => $this->faker->company(),
            'sector' => $this->faker->randomElement($sectors),
            'country' => $this->faker->randomElement(['France', 'Belgique', 'Suisse']),
            'city' => $this->faker->randomElement($cities),
            'description' => 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.',
            'website' => $this->faker->url(),
            'employee_count' => $this->faker->randomElement(['1-10', '11-50', '51-200', '200+']),
            'hiring_needs' => 'Talents full-stack, mobile et backend pour missions longue durée.',
        ];
    }
}
