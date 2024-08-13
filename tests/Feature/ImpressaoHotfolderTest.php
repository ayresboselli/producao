<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Services\ImpressaoHotfolderService;

class ImpressaoHotfolderTest extends TestCase
{
    private function Criar()
    {
        return ImpressaoHotfolderService::Salvar(
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

        $result = ImpressaoHotfolderService::Buscar($teste->id);

        $this->assertTrue($result != null);
    }

    public function test_listar()
    {
        $teste = $this->Criar();

        $result = ImpressaoHotfolderService::Listar();
        $this->assertTrue(count($result) > 0);
    }

    public function test_deletar()
    {
        $result = $this->Criar();

        $this->assertTrue(
            ImpressaoHotfolderService::Deletar($result->id)
        );
    }
}
