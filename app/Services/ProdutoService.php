<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Produto;

class ProdutoService
{
    /**
     * Listar produtos
     * @return Collection
     */
    public static function Listar(): Collection
    {
        return Produto::get();
    }

    /**
     * Buscar produto
     * @param int|null $id
     * @return Produto|null
     */
    public static function Buscar(int|null $id): Produto|null
    {
        return Produto::find($id);
    }

    /**
     * Salvar produto
     * @param object $parametro
     * @return Produto
     */
    public static function Salvar(object $parametro): Produto
    {
        try
        {
            $obj = Produto::find($parametro->id|null);

            if (is_null($obj))
                $obj = new Produto();

            $obj->id_externo = $parametro->id_externo;
            $obj->imposicao_tipo_id = $parametro->imposicao_tipo_id;
            $obj->imposicao_nome_id = $parametro->imposicao_nome_id;
            $obj->impressao_hotfolder_id = $parametro->impressao_hotfolder_id;
            $obj->impressao_substrato_id = $parametro->impressao_substrato_id;
            $obj->titulo = $parametro->titulo;
            $obj->largura = $parametro->largura;
            $obj->altura = $parametro->altura;
            $obj->sangr_sup = $parametro->sangr_sup;
            $obj->sangr_inf = $parametro->sangr_inf;
            $obj->sangr_esq = $parametro->sangr_esq;
            $obj->sangr_dir = $parametro->sangr_dir;
            $obj->disposicao = $parametro->disposicao;
            $obj->renomear = isset($parametro->renomear)&&$parametro->renomear?true:false;
            $obj->sem_dimensao = isset($parametro->sem_dimensao)&&$parametro->sem_dimensao?true:false;

            $obj->save();

            return $obj;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Duplicar produto
     * @param int $id
     * @return Produto
     */
    public static function Duplicar(int $id): Produto|null
    {
        try
        {
            $origem = Produto::find($id);

            if ($origem)
            {
                $origem->id = null;
                return ProdutoService::Salvar($origem);
            }

            return null;
        }
        catch(Except $e)
        {
            throw $e;
        }
    }

    /**
     * Deletar produto
     * @param int $id
     * @return bool
     */
    public static function Deletar(int $id): bool
    {
        try
        {
            $obj = Produto::find($id);

            if ($obj &&
                count($obj->itens) == 0 &&
                count($obj->tabelas) == 0
            ) {
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
