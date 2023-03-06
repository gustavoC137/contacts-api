<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::factory()
            ->count(25)
            ->has(Contact::factory()
                ->count(3)
                ->state(new Sequence(
                    ['contact' => fake()->e164PhoneNumber(), 'contact_type' => 'whatsapp'],
                    ['contact' => fake()->cellphoneNumber(false), 'contact_type' => 'cellphone'],
                    ['contact' => fake()->email(), 'contact_type' => 'email'],
                ))
            )
            ->create();
    }
}
