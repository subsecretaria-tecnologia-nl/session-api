<?php

use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("catalog_user_roles")->insert([
            'name' => 'ciudadano',
            'description' => 'Ciudadano'
        ]);
        DB::table("catalog_user_roles")->insert([
            'name' => 'notary_titular',
            'description' => 'Notario'
        ]);
        DB::table("catalog_user_roles")->insert([
            'name' => 'funcionario',
            'description' => 'Funcionario'
        ]);
        DB::table("catalog_user_roles")->insert([
            'name' => 'company',
            'description' => 'Compañia'
        ]);
        DB::table("catalog_user_roles")->insert([
            'name' => 'notary_substitute',
            'description' => 'Substituto'
        ]);
        DB::table("catalog_user_roles")->insert([
            'name' => 'notary_users',
            'description' => 'Usuario de Notario'
        ]);
    }
}
