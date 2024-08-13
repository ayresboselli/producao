<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\ImposicaoTipoService;
use App\Services\ImposicaoNomeService;
use App\Services\ImpressaoHotfolderService;
use App\Services\ImpressaoSubstratoService;
use App\Services\ProdutoService;

class ProdutoTest extends TestCase
{
    private function Criar()
    {
        $tipo = ImposicaoTipoService::Salvar(
            (object) [
                'id' => null,
                'titulo' => 'Teste',
                'descricao' => 'Descrição'
            ]
        );

        $nome = ImposicaoNomeService::Salvar(
            (object) [
                'id' => null,
                'imposicao_tipo_id' => $tipo->id,
                'titulo' => 'Teste',
                'descricao' => 'Descrição'
            ]
        );

        $hotFolder = ImpressaoHotfolderService::Salvar(
            (object) [
                'id' => null,
                'titulo' => 'Teste',
                'descricao' => 'Descrição'
            ]
        );

        $substrato = ImpressaoSubstratoService::Salvar(
            (object) [
                'id' => null,
                'titulo' => 'Teste',
                'descricao' => 'Descrição'
            ]
        );

        return ProdutoService::Salvar(
            (object) [
                'id' => null,
                'id_externo' => 123,
                'imposicao_tipo_id' => $tipo->id,
                'imposicao_nome_id' => $nome->id,
                'impressao_hotfolder_id' => $hotFolder->id,
                'impressao_substrato_id' => $substrato->id,
                'titulo' => 'Produto teste',
                'sem_dimensao' => 0,
                'largura' => 100,
                'altura' => 150,
                'sangr_sup' => 0,
                'sangr_inf' => 0,
                'sangr_esq' => 0,
                'sangr_dir' => 0,
                'disposicao' => 0,
                'renomear' => 0,
            ]
        );
    }

    public function test_inserir()
    {
        $result = $this->Criar();

        $this->assertTrue($result->id != null);
    }

    public function test_buscar()
    {
        $teste = $this->Criar();

        $result = ProdutoService::Buscar($teste->id);

        $this->assertTrue($result != null);
    }

    public function test_listar()
    {
        $teste = $this->Criar();

        $result = ProdutoService::Listar();
        $this->assertTrue(count($result) > 0);
    }

    public function test_duplicar()
    {
        $p1 = $this->Criar();
        $p2 = ProdutoService::Duplicar($p1->id);

        $this->assertTrue($p1->id != $p2->id);
    }

    public function test_deletar()
    {
        $result = $this->Criar();

        $this->assertTrue(
            ProdutoService::Deletar($result->id)
        );
    }
}
