<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Servico;

class ServicoService
{
    /**
     * Listar tipos de imposição
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return Servico::get();
    }

    /**
     * Buscar tipo de imposição
     * @param int|null $id
     * @return Servico|null
     */
    public static function Buscar(int|null $id): Servico|null
    {
        return Servico::find($id);
    }

    /**
     * Salvar tipo de imposição
     * @param object $parametro
     * @return Servico
     */
    public static function Salvar(object $parametro): Servico
    {
        try
        {
            $obj = Servico::find($parametro->id|null);

            if (is_null($obj))
                $obj = new Servico();

            $obj->id_externo = $parametro->id_externo;
            $obj->titulo = $parametro->titulo;

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
            $obj = Servico::find($id);

            if ($obj && count($obj->itens) == 0) {
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
