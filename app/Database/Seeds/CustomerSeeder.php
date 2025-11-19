<?php

namespace App\Database\Seeds;

use App\Models\CustomerModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customerModel = new CustomerModel();
        $faker = Factory::create('id_ID');

        for ($i = 0; $i < 20; $i++) {
            $data = [
                'customer_name'         => $faker->name,
                'customer_email'        => $faker->unique()->email,
                'customer_password'     => password_hash('123', PASSWORD_DEFAULT),
                'customer_phone'        => $faker->phoneNumber,
                'customer_dob'          => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'customer_gender'       => $faker->randomElement(['male', 'female']),
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s')
            ];

            $customerModel->insert($data);
        }
    }
}
