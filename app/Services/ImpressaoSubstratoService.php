<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\ImpressaoSubstrato;

class ImpressaoSubstratoService
{
    /**
     * Listar substratos de impressão
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return ImpressaoSubstrato::get();
    }

    /**
     * Buscar substrato de impressão
     * @param int|null $id
     * @return ImpressaoSubstrato|null
     */
    public static function Buscar(int|null $id): ImpressaoSubstrato|null
    {
        return ImpressaoSubstrato::find($id);
    }

    /**
     * Salvar substrato de impressão
     * @param object $parametro
     * @return ImpressaoSubstrato
     */
    public static function Salvar(object $parametro): ImpressaoSubstrato
    {
        try
        {
            $obj = ImpressaoSubstrato::find($parametro->id|null);

            if (is_null($obj))
                $obj = new ImpressaoSubstrato();

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
     * Deletar substrato de impressão
     * @param int $id
     * @return bool
     */
    public static function Deletar(int $id): bool
    {
        try
        {
            $obj = ImpressaoSubstrato::find($id);

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
