<?php
namespace Opeepl\BackendTest\Service;

/**
 * Main entrypoint for this library.
 */


class ExchangeRateService {

    /**
     * Select the operative exchange from a list of available: 
     * 'EXCHANGERATES_DATA'
     * 'FIXER'
     */
    const SET_EXCHANGE = 'EXCHANGERATES_DATA';          

    private $selected_exchanges;
    private $link_available;

    //Select the exchange from the exchanges available
    public function __construct() {

    $this -> link_available = array ('EXCHANGERATES_DATA' => new Exchangerates_data (), 
                                     'FIXER' => new Fixer ());                                      
    $this -> selected_exchanges = $this -> link_available [self:: SET_EXCHANGE];
    }

    /**
     * Return all supported currencies
     *
     * @return array<string>
     */

    public function getSupportedCurrencies(): array {
        
        $link = $this -> selected_exchanges -> get_link_support();

        $curl = curl_init();

        //Set-up HTTP_GET request, switching off the check of the certificates because HTTPS use SSL certificates, MITM attack risk!!
        curl_setopt_array($curl, array(          
          CURLOPT_URL => $link,
          CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            $this -> selected_exchanges -> get_apiKey()
          ),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYHOST => false,        
          CURLOPT_SSL_VERIFYPEER => false,     
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ));

        //Make HTTP_GET request      
        $response = curl_exec($curl);         

        curl_close($curl);              

        //From JSON to array
        $response = json_decode($response,true);           

        //Sanity check
        $response= $this -> selected_exchanges -> check_integrity_support($response);

        //Get result or give a error-result as empty array
        if ($response ['success'] == true)
             $response = $this -> selected_exchanges -> get_result_support($response);          
         else
             $response= [];
        
        return $response;

    }


    /**
     * Given the $amount in $fromCurrency, it returns the corresponding amount in $toCurrency.
     *
     * @param int $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return int
     */


    public function getExchangeAmount(int $amount, string $fromCurrency, string $toCurrency): int {

        $link = $this -> selected_exchanges -> get_link_exchange($toCurrency, $fromCurrency, $amount);

        $curl = curl_init();

        //Set-up HTTP_GET request, switching off the check of the certificates because HTTPS use SSL certificates, MITM attack risk!!
        curl_setopt_array($curl, array(
          CURLOPT_URL => $link,
          CURLOPT_HTTPHEADER => array(
            "Content-Type: text/plain",
            $this -> selected_exchanges -> get_apiKey()
          ),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYHOST => false,         
          CURLOPT_SSL_VERIFYPEER => false,           
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ));

        //Make HTTP_GET request             
        $response = curl_exec($curl);

        curl_close($curl);

        //From JSON to array
        $response = json_decode($response,true);

        //Sanity check
        $response= $this -> selected_exchanges -> check_integrity_exchange($response);

        //Get result or give a error-result as -1
        if ($response ['success'] == true)
            $response = $response ['result'];
        else
            $response = -1; 

        return $response;
    }
}



