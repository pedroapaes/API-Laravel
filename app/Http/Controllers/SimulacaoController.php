<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class SimulacaoController extends Controller
{
    public function instituicoes()
    {
        //Validacao se o arquivo contendo as instituicoes esta com o nome correto e no diretorio esperado.
        //Error 404: Recurso nao encontrado.
        if (!Storage::exists('public/instituicoes.json')) {
            return response()->json(['erro' => 'Arquivo não encontrado'], 404);
        }

        //Atribui o arquivo json a uma variavel e o converte em uma array.
        $json = Storage::get('public/instituicoes.json');
        $instituicoes = json_decode($json, true);

        //verifica se a estrutura do arquivo estava correta e foi convertida com sucesso.
        //Error 422: A solicitacao possui erros semanticos ou nao atende as condicoes determinadas.
        if ($instituicoes === null) {
            return response()->json(['erro' => 'JSON inválido'], 422);
        }

        //Retorna a array com todos os dados do arquivo
        return response()->json($instituicoes);
    }

    public function convenios()
    {
        //Validacao se o arquivo contendo os convenios esta com o nome correto e no diretorio esperado.
        //Error 404: Recurso nao encontrado.
        if (!Storage::exists('public/convenios.json')) {
            return response()->json(['erro' => 'Arquivo não encontrado'], 404);
        }

        //Atribui o arquivo json a uma variavel e o converte em uma array.
        $json = Storage::get('public/convenios.json');
        $convenios = json_decode($json, true);

        //verifica se a estrutura do arquivo estava correta e foi convertida com sucesso.
        //Error 422: A solicitacao possui erros semanticos ou nao atende as condicoes determinadas.
        if ($convenios === null) {
            return response()->json(['erro' => 'JSON inválido'], 422);
        }

        //Retorna a array com todos os dados do arquivo
        return response()->json($convenios);
    }


    public function simulacaoCredito(Request $request)
    {
        //Parametrizacao dos atributos
        $request->validate([
            'valor_emprestimo' => 'required|numeric|min:1',
            'instituicoes' => 'nullable|array',
            'convenios' => 'nullable|array',
            'parcela' => 'nullable|integer|min:1',
        ]);

        //Setando os valores passados nas variaveis
        $valorEmprestimo = $request->valor_emprestimo;
        $instituicoesFiltradas = $request->instituicoes ?? [];
        $conveniosFiltrados = $request->convenios ?? [];
        $parcelasSolicitadas = $request->parcela;

        //Validacao se o arquivo contendo as taxas esta com o nome correto e no diretorio esperado.
        //Error 404: Recurso nao encontrado.
        if (!Storage::exists('public/taxas_instituicoes.json')) {
            return response()->json(['erro' => 'Arquivo de taxas não encontrado'], 404);
        }

        //Atribui o arquivo json a uma variavel e o converte em uma array.
        $jsonTaxas = Storage::get('public/taxas_instituicoes.json');
        $taxas = json_decode($jsonTaxas, true);

        //verifica se a estrutura do arquivo estava correta e foi convertida com sucesso.
        //Error 422: A solicitacao possui erros semanticos ou nao atende as condicoes determinadas.
        if ($taxas === null) {
            return response()->json(['erro' => 'JSON de taxas inválido'], 422);
        }

        $simulacoes = [];

        //Iteracao realizada para cada instituicao
        foreach ($taxas as $taxa) {
            //Filtrando de acordo com os parametros passados no request
            //1 Condicao - Foi passado uma ou N instituicoes como filtro
            //2 Condicao - Foi passado um ou N convenios como filtro
            //3 Condicao - Foi passado um numero especifico de parcelas como filtro
            if (
                (!empty($instituicoesFiltradas) && !in_array($taxa['instituicao'], $instituicoesFiltradas)) ||
                (!empty($conveniosFiltrados) && !in_array($taxa['convenio'], $conveniosFiltrados)) ||
                ($parcelasSolicitadas && $taxa['parcelas'] != $parcelasSolicitadas)
            ) {
                continue;
            }

            //Calcula o valor da parcela
            //Racional - Valor solicitado multiplicado pelo o valor do coeficiente
            $valorParcela = round($valorEmprestimo * $taxa['coeficiente'], 2);

            //Setando as informacoes para insercao no JSON
            $simulacoes[] = [
                'taxa' => $taxa['taxaJuros'],
                'parcelas' => $taxa['parcelas'],
                'valor_parcela' => $valorParcela,
                'convenio' => $taxa['convenio'],
            ];
        }

        //Validacao se a simulacao ocorreu com sucesso.
        //Error 404: Recurso nao encontrado.
        if (empty($simulacoes)) {
            return response()->json(['erro' => 'Nenhuma simulação encontrada para os parâmetros informados'], 404);
        }

        //Retorna JSON com as simulacoes realizadas
        return response()->json($simulacoes);
    }





}
