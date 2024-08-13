<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\ImposicaoTipoService;

class ImposicaoTipoTest extends TestCase
{
    private function Criar()
    {
        return ImposicaoTipoService::Salvar(
            (object) [
                'id' => null,
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

        $result = ImposicaoTipoService::Buscar($teste->id);

        $this->assertTrue($result != null);
    }

    public function test_listar()
    {
        $teste = $this->Criar();

        $result = ImposicaoTipoService::Listar();
        $this->assertTrue(count($result) > 0);
    }

    public function test_deletar()
    {
        $result = $this->Criar();

        $this->assertTrue(
            ImposicaoTipoService::Deletar($result->id)
        );
    }
}
