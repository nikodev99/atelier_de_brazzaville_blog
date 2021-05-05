<?php

use App\Auth\PasswordHash;
use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $this->table('users')
            ->insert([
                'username'      =>  'admin',
                'email'         =>  'admin@latelierbrazzaville.com',
                'password'      =>  PasswordHash::hash('admin'),
                'first_name'    =>  'admin',
                'last_name'     =>  'admin',
                'birth_date'    =>  '1976-04-16',
                'country'       =>  'France',
                'city'          =>  'Bastia',
                'address'       =>  '16 rue saint-andré 7002 aix de gauche'
            ])
        ->save()
        ;
    }
}
