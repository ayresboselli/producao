<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('funcoes')->insert(['chave' => 'imp_ferr_ver', 'descricao' => 'Ver ferramentas de imosição', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_ferr_edit', 'descricao' => 'Editar ferramentas de imosição', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_mod_ver', 'descricao' => 'Ver modelos de imposição', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_mod_edit', 'descricao' => 'Editar modelos de imposição', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_hotf_ver', 'descricao' => 'Ver HotFolders de impressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_hotf_edit', 'descricao' => 'Editar HotFolders de impressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_substr_ver', 'descricao' => 'Ver substratos de impressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'imp_substr_edit', 'descricao' => 'Editar substratos de impressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'produto_ver', 'descricao' => 'Ver produtos', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'produto_edit', 'descricao' => 'Editar produtos', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'pedido_ver', 'descricao' => 'Ver pedidos', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'pedido_edit', 'descricao' => 'Editar pedidos', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'recorte_ver', 'descricao' => 'Ver recortes', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'recorte_edit', 'descricao' => 'Editar recorte', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'upload_ver', 'descricao' => 'Ver uploads', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'upload_edit', 'descricao' => 'Editar uploads', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'funcoes_ver', 'descricao' => 'Ver funções', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'perfis_ver', 'descricao' => 'Ver perfis', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'perfis_edit', 'descricao' => 'Editar perfis', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'usuarios_ver', 'descricao' => 'Ver usuários', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'usuarios_edit', 'descricao' => 'Editar usuários', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'alt_senhas', 'descricao' => 'Alterar senhas de usuários', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'dashboard', 'descricao' => 'Dashboard', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'servico_ver', 'descricao' => 'Ver serviços', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'servico_edit', 'descricao' => 'Editar serviços', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'cliente_ver', 'descricao' => 'Ver clientes', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'cliente_edit', 'descricao' => 'Editar clientes', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'arquiv_edit', 'descricao' => 'Arquivamento', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'reimp_ver', 'descricao' => 'Ver reimpressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'reimp_edit', 'descricao' => 'Editar reimpressão', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'rel_mg_precos', 'descricao' => 'Relatório de preços de produtos', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'dsh_fin', 'descricao' => 'Dashboar financeiro', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'gsl_ver', 'descricao' => 'Ver Gestão de Sistemas Legado', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'gsl_edit', 'descricao' => 'Editar Gestão de Sistemas Legado', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'integ_ver', 'descricao' => 'Ver integrações', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'integ_edit', 'descricao' => 'Editar integrações', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'tab_preco_ver', 'descricao' => 'Ver tabela de preços', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'tab_preco_edit', 'descricao' => 'Editar tabela de preços', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'guiasOP_ver', 'descricao' => 'Ver Gerar quias de OP', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'guiasOP_edit', 'descricao' => 'Editar Ver Gerar quias de OP', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        DB::table('funcoes')->insert(['chave' => 'status_servicos', 'descricao' => 'Status de Serviços', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
