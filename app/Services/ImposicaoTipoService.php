<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\ImposicaoTipo;

class ImposicaoTipoService
{
    /**
     * Listar tipos de imposição
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return ImposicaoTipo::get();
    }

    /**
     * Buscar tipo de imposição
     * @param int|null $id
     * @return ImposicaoTipo|null
     */
    public static function Buscar(int|null $id): ImposicaoTipo|null
    {
        return ImposicaoTipo::find($id);
    }

    /**
     * Salvar tipo de imposição
     * @param object $parametro
     * @return ImposicaoTipo
     */
    public static function Salvar(object $parametro): ImposicaoTipo
    {
        try
        {
            $obj = ImposicaoTipo::find($parametro->id|null);

            if (is_null($obj))
                $obj = new ImposicaoTipo();

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
     * Deletar tipo de imposição
     * @param int $id
     * @return bool
     */
    public static function Deletar(int $id): bool
    {
        try
        {
            $obj = ImposicaoTipo::find($id);

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
