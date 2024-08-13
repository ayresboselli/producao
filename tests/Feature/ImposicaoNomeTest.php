<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\ImposicaoNomeService;
use App\Services\ImposicaoTipoService;

class ImposicaoNomeTest extends TestCase
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

        return ImposicaoNomeService::Salvar(
            (object) [
                'id' => null,
                'imposicao_tipo_id' => $tipo->id,
                'titulo' => 'Teste',
                'descricao' => 'Descrição'
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

        $result = ImposicaoNomeService::Buscar($teste->id);

        $this->assertTrue($result != null);
    }

    public function test_listar()
    {
        $result = $this->Criar();

        $result = ImposicaoNomeService::Listar();
        $this->assertTrue(count($result) > 0);
    }

    public function test_deletar()
    {
        $result = $this->Criar();

        $this->assertTrue(
            ImposicaoNomeService::Deletar($result->id)
        );
    }
}
