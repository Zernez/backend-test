<?php
namespace Opeepl\BackendTest\Service;

use Opeepl\BackendTest\Service\ExchangeRateService;

class Exchangerates_data extends ExchangeRateService{

    // Set-up data from the API provider
    const LINK_AVAILABLE = 'https://api.apilayer.com/exchangerates_data/';
    const API_KEY= "apikey: fTDc2JZZHZFtdG5WwX8LDauWVMrFK5Cz";
    
    private $link;
    private $apiKey;

    public function __construct() {
        $this -> link = self :: LINK_AVAILABLE;
        $this -> apiKey = self :: API_KEY;
      }

    // HTTPS link builders      
    protected function get_link_support (): string {

        return $this -> link . 'symbols';
    }

    protected function get_link_exchange ($toCurrency, $fromCurrency, $amount): string {

        return $this -> link . 'convert?to=' . $toCurrency . '&from=' . $fromCurrency . '&amount=' . (string)$amount;;
    }

    protected function get_apiKey (): string {

        return $this -> apiKey;
    }

    protected function check_integrity_support ($response): array {
        
        // Coalescence operator check for NULL and fill with a valid error in case of NULL
        $response = $response ?? array('success' => false);                
        
        // Check for error and fill with a general error  
        if (array_key_exists('error', $response)){
            return array('success' => false);              
        }  
        else{
            return $response;
        }
    }

    protected function get_result_support ($response): array {

        return array_keys($response ['symbols']);
    }

    protected function check_integrity_exchange ($response): array {

        // Coalescence operator check for NULL and fill with a valid error in case of NULL
        $response = $response ?? array('success' => false);                
        
        // Check for error and fill with a general error  
        if (array_key_exists('error', $response)){
            return array('success' => false);              
        }  
        else{
            return $response;
        }
    }

    protected function get_result_exchange ($response): array {

        return array_keys($response ['result']);
    }
}