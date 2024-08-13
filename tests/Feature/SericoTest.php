<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\ServicoService;

class ServicoTest extends TestCase
{
    private function Criar()
    {
        return ServicoService::Salvar(
            (object) [
                'id' => null,
                'id_externo' => 123,
                'titulo' => 'Teste'
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

        $result = ServicoService::Buscar($teste->id);

        $this->assertTrue($result != null);
    }

    public function test_listar()
    {
        $teste = $this->Criar();

        $result = ServicoService::Listar();
        $this->assertTrue(count($result) > 0);
    }

    public function test_deletar()
    {
        $result = $this->Criar();

        $this->assertTrue(
            ServicoService::Deletar($result->id)
        );
    }
}
