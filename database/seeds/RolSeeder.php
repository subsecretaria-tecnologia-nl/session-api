<?php

use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
			DB::table('rol')->insert([
        'name' => 'Administrador',
        'activo'=>1
			]);
			DB::table('rol')->insert([
        'name' => 'Usuario',
        'activo'=>1
			]);
			DB::table('rol')->insert([
        'name' => 'Subusuario',
        'activo'=>1
      ]);

    }
}
