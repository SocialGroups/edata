<?php

namespace Medcenter\Application\Helper;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;


class HelperData implements Routable
{

    function getCurl($url, $json, $action)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, ZDURL.$url);
        curl_setopt($ch, CURLOPT_USERPWD, ZDUSER."/token:".ZDAPIKEY);
        switch($action){
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            default:
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($output);
        return $decoded;
    }

    public function tratamentsZdFiled($fieldType,$fieldData)
    {

        $convertField = array(

            'tipo_chamado'          => '23628325',
            'equipe_responsavel'    => '23568679',
            'qty_horas_orcadas'     => '23630565',
            'status_de_orcamento'   => '23571619',
            'desenvolvedor'         => '23730095',
            'qty_horas_orcadas'     => '23630965'

        );

        $fieldId = $convertField["$fieldType"];

        foreach($fieldData as $data){

            if($data->id == $fieldId){

                return $data->value;
            }

        }

    }

}
