<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\ImpressaoHotfolder;

class ImpressaoHotfolderService
{
    /**
     * Listar hotfolders de impressão
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return ImpressaoHotfolder::get();
    }

    /**
     * Buscar hotfolder de impressão
     * @param int|null $id
     * @return ImpressaoHotfolder|null
     */
    public static function Buscar(int|null $id): ImpressaoHotfolder|null
    {
        return ImpressaoHotfolder::find($id);
    }

    /**
     * Salvar hotfolder de impressão
     * @param object $parametro
     * @return ImpressaoHotfolder
     */
    public static function Salvar(object $parametro): ImpressaoHotfolder
    {
        try
        {
            $obj = ImpressaoHotfolder::find($parametro->id|null);

            if (is_null($obj))
                $obj = new ImpressaoHotfolder();

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
     * Deletar hotfolder de impressão
     * @param int $id
     * @return bool
     */
    public static function Deletar(int $id): bool
    {
        try
        {
            $obj = ImpressaoHotfolder::find($id);

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
