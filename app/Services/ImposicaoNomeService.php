<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\ImposicaoNome;

class ImposicaoNomeService
{
    /**
     * Listar nomes de imposição
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return ImposicaoNome::get();
    }

    /**
     * Buscar nome de imposição
     * @param int|null $id
     * @return ImposicaoNome|null
     */
    public static function Buscar(int|null $id): ImposicaoNome|null
    {
        return ImposicaoNome::with('tipo')->find($id);
    }

    /**
     * Salvar nome de imposição
     * @param object $parametro
     * @return ImposicaoNome
     */
    public static function Salvar(object $parametro): ImposicaoNome
    {
        try
        {
            $obj = ImposicaoNome::find($parametro->id|null);

            if (is_null($obj))
                $obj = new ImposicaoNome();

            $obj->imposicao_tipo_id = $parametro->imposicao_tipo_id;
            $obj->titulo = $parametro->titulo;
            $obj->descricao = $parametro->descricao;
            $obj->save();

            return $obj;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Deletar nome de imposição
     * @param int $id
     * @return bool
     */
    public static function Deletar(int $id): bool
    {
        try
        {
            $obj = ImposicaoNome::find($id);

            if ($obj && count($obj->produtos) == 0) {
                $obj->delete();
                return true;
            }

            return false;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }
}
