<?php

namespace Medcenter\Application\Model;

use Respect\Rest\Routable;
use \InvalidArgumentException as Argument;
use Respect\Config\Container;
use Respect\Relational\Db;
use Respect\Relational\Sql;
use Medcenter\Application\Helper\HelperData;

define("ZDAPIKEY", "sGSGNvOvUr37WMlXQp2zvPfFNEdDMoagAmu1dMry");
define("ZDUSER", "ti@e-smart.com.br");
define("ZDURL", "https://smartecommerce.zendesk.com/api/v2");

class GetZendesckData implements Routable
{
    private $mapper;

    public function __construct()
    {

        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
        $this->helper = new HelperData();
    }

    public function getUserId($userAssigned)
    {

        if($userAssigned == null OR $userAssigned == 12){

            return 12;

        }

        $usuario = $this->mapper->user(array('user_name'=>$userAssigned))->fetch();

        return $usuario->id;

    }

    public function updateFieldDate($ticketId,$fieldLabel,$fieldValue)
    {

        $ticket = $this->mapper->ticket_field_log(array('ticket_id' => $ticketId,'field_label' => $fieldLabel, 'field_value' => $fieldValue))->fetch();

        if($ticket == '')
        {
            $ticket = new \stdClass;
            $ticket->ticket_id   = $ticketId;
            $ticket->field_label = $fieldLabel;
            $ticket->field_value = $fieldValue;
            $ticket->date        = date('Y-m-d H:i:s');

            $this->mapper->ticket_field_log->persist($ticket);
            $this->mapper->flush();

        }



    }

    public function insertTicket($ticketTitle,$userAssigned,$ticketId)
    {

        $teste = $this->checkTicket($ticketId);

        if($this->checkTicket($ticketId) == true)
        {

            $ticket = new \stdClass;
            $ticket->user_id = $this->getUserId($userAssigned);
            $ticket->name = $ticketTitle;
            $ticket->ticket_id = $ticketId;


            $this->mapper->ticket->persist($ticket);
            $this->mapper->flush();

            $getLastId = $this->mapper->ticket()->fetch(Sql::orderBy('id')->desc()->limit(1));

            return $getLastId->id;

        }

        $ticket = $this->mapper->ticket(array('ticket_id' => $ticketId))->fetch();

        return $ticket->id;

    }

    public function checkTicket($ticketId)
    {

        $ticket = $this->mapper->ticket(array('ticket_id' => $ticketId))->fetch();

        if($ticket == null)
        {
            return true;

        }

        return false;

    }

    public function checkField($ticketId,$key)
    {

        $ticketField = $this->mapper->ticket_field(array('ticket_id' => $ticketId,'label' => $key))->fetch();

        if($ticketField == '')
        {
            return false;

        }

        return $ticketField->id;

    }

    public function setFields($ticketId,$dataFields)
    {

        foreach($dataFields as $data)
        {

            foreach($data as $key => $value){

                $ticketFieldId = $this->checkField($ticketId,$key);

                if($ticketFieldId){

                    $ticket = $this->mapper->ticket_field[$ticketFieldId]->fetch();
                    $ticket->ticket_id = $ticketId;
                    $ticket->label = $key;
                    $ticket->value = $value;
                    $this->mapper->ticket_field->persist($ticket);
                    $this->mapper->flush();

                }else{

                    $ticket = new \stdClass;
                    $ticket->ticket_id = $ticketId;
                    $ticket->label = $key;
                    $ticket->value = $value;

                    $this->mapper->ticket_field->persist($ticket);
                    $this->mapper->flush();

                }

                $this->updateFieldDate($ticketId,$key,$value);

            }

        }

    }

    public function checkTicketStatus($ticketId,$status,$date)
    {


        $ticketField = $this->mapper->ticket_status(array('ticket_id' => $ticketId,'status' => $status,'date'=> $date))->fetch();

        if($ticketField == '')
        {
            return true;

        }

        return false;

    }

    public function setTicketStatus($ticketId,$status,$date)
    {
        $ticket = new \stdClass;
        $ticket->ticket_id = $ticketId;
        $ticket->status = $status;
        $ticket->date   = $date;

        $this->mapper->ticket_status->persist($ticket);
        $this->mapper->flush();
    }

    public function setData($data)
    {

        $ticketId = $this->insertTicket($data['title'],$data['desenvolvedor'],$data['ticket_id']);

        $dataFields = array(

            array(

                'equipe_responsavel'    => ($data['equipe_responsavel'])

            ),
            array(
                'qty_horas_orcadas'     => $data['qty_horas_orcadas']

            ),
            array(
                'status_de_orcamento'   => $data['status_de_orcamento']

            ),

            array(
                'desenvolvedor'         => $data['desenvolvedor']

            ),
            array(
                'qty_horas_orcadas'     => $data['qty_horas_orcadas']


            )

        );

        $this->setFields($ticketId,$dataFields);

        $this->setTicketStatus($ticketId,$data['status'],$data['data']);

    }


    public function getData()
    {

        $getLastId = $this->mapper->ticket()->fetch(Sql::orderBy('id')->desc()->limit(1));

        $i = $getLastId->ticket_id;

        $controller = $i+50;

        while ( $i <= $controller ) {

        $i++;

        $getData = $this->helper->getCurl("/tickets/$i.json", '', "GET");

        if(isset($getData->ticket)){

            $setData = array(

                'ticket_id'             => $getData->ticket->id,
                'url'                   => $getData->ticket->url,
                'data'                  => $getData->ticket->created_at,
                'title'                 => $getData->ticket->subject,
                'status'                => $getData->ticket->status,
                'tipo_chamado'          => $this->helper->tratamentsZdFiled('tipo_chamado',$getData->ticket->fields),
                'equipe_responsavel'    => $this->helper->tratamentsZdFiled('equipe_responsavel',$getData->ticket->fields),
                'qty_horas_orcadas'     => $this->helper->tratamentsZdFiled('qty_horas_orcadas',$getData->ticket->fields),
                'status_de_orcamento'   => $this->helper->tratamentsZdFiled('status_de_orcamento',$getData->ticket->fields),
                'desenvolvedor'         => ($this->helper->tratamentsZdFiled('desenvolvedor',$getData->ticket->fields) == null)? 12 : $this->helper->tratamentsZdFiled('desenvolvedor',$getData->ticket->fields),
                'qty_horas_orcadas'     => $this->helper->tratamentsZdFiled('qty_horas_orcadas',$getData->ticket->fields)

            );

            $this->setData($setData);



        }
        }

    }

}