<?php

namespace App\Service;

use App\Util\HttpCode;
use Exception;

class Easynvest
{
    private $baseUrl = 'https://mdgateway.easynvest.com.br/iwg/snapshot/';

    /**
     * Retorna os dados em tempo real das açoes solicitadas
     * @param string $stock Ex: "PETR4" ou "ITUB4,PETR4,BBAS3"
     * @throws Exception
     */
    public function getStockInformation(string $stock): void
    {
        $stock = str_replace(' ', '', $stock);
        $stock = explode(',', $stock);

        $url = $this->createConsultURL($stock);
        $jsonObject = $this->serviceConsult($url);
        print_r($jsonObject);
    }

    /**
     * Cria a url de consulta das ações selecionadas
     * @param array $stocks
     * @return string
     */
    private function createConsultURL(array $stocks): string
    {
        $url = $this->baseUrl;
        $url .= '?t=webgateway&c=5705796&q=';

        foreach ($stocks as $stock) {
            $url .= $stock . ',29,0,10|';
        }

        return $url;
    }

    /**
     * Pega as informaçoes em tempo de execução do serviço da easynvest
     * @param string $url
     * @return mixed
     * @throws Exception
     */
    private function serviceConsult(string $url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception($err, HttpCode::INTERNAL_SERVER_ERROR);
        }

        return json_decode($response);
    }
}