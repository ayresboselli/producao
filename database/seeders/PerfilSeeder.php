<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perfis')->insert([
            'titulo' => 'Administrador',
            'descricao' => 'Administrador do sistema'
        ]);

        for ($i = 1; $i <= 41; $i++)
        {
            DB::table('perfil_funcao')->insert([
                'perfil_id' => 1,
                'funcao_id' => $i,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
