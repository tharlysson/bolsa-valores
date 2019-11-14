<?php

namespace App\Service;

use App\Entity\Stock;
use App\Util\HttpCode;
use Exception;

class Easynvest
{
    private $snapShotURL;
    private $historyURL;
    private $accessToken;

    /**
     * Easynvest constructor.
     */
    public function __construct()
    {
        $this->snapShotURL = getenv('EASYNVEST_SNAPSHOT_URL');
        $this->historyURL = getenv('EASYNVEST_HISTORIC_URL');
        $this->accessToken = getenv('EASYNVEST_ACCESS_TOKEN');
    }

    /**
     * Retorna os dados em tempo real das açoes solicitadas
     * @param string $stock Ex: "PETR4" ou "ITUB4,PETR4,BBAS3"
     * @return array
     * @throws Exception
     */
    public function getStockInformation(string $stock): array
    {
        $stock = str_replace(' ', '', $stock);
        $stock = explode(',', $stock);

        $url = $this->createConsultURL($stock);
        $jsonObject = $this->serviceConsult($url);

        $return = [];
        foreach ($jsonObject->Value as $item) {
            $stockObj = new Stock();
            $stockObj->code = $item->S;
            $stockObj->name = $item->Ps->SD;
            $stockObj->price = $item->Ps->P;
            $stockObj->open = $item->Ps->OP;
            $stockObj->maxDay = $item->Ps->MxP;
            $stockObj->minDay = $item->Ps->MnP;
            $stockObj->lastUpdate = $item->UT;

            $teste = $this->getDetailsFromPeriod($item->Ps->SD, 2);

            array_push($return, $stockObj);
        }

        return $return;
    }

    /**
     * Cria a url de consulta das ações selecionadas
     * @param array $stocks
     * @return string
     */
    private function createConsultURL(array $stocks): string
    {
        $url = $this->snapShotURL;
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

    /**
     * @param string $stock
     * @param int $period
     * @return mixed
     * @throws Exception
     */
    private function getDetailsFromPeriod(string $stock, int $period)
    {
        $authorization = "Authorization: Bearer {$this->accessToken}";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->historyURL . "$stock/$period",
            CURLOPT_HTTPHEADER => [$authorization],
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => true,
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