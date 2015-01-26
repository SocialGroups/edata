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

class GetJiraData implements Routable
{
    private $mapper;

    public function __construct()
    {

        $this->c = new Container(CONFIG_DIR . '/config.ini');
        $this->mapper = $this->c->mapper;
        $this->helper = new HelperData();
    }

    public function tratamentDate($data)
    {

        $dateTratament = explode("T", $data);
        $hourTratament = explode(".", $dateTratament[1]);

        $date =  $dateTratament[0].' '.$hourTratament[0];

        $returnDate = date('Y-m-d H:i:s', strtotime($date));

        return $returnDate;

    }

    public function getOnId($url)
    {

        $getON = explode('ON-',$url);
        $ON     = explode('/',$getON['1']);

        return $ON[0];

    }

    public function checkData($jiraId)
    {

        $check = $this->mapper->jira(array('jira_id'=>$jiraId))->fetch();

        if($check){

            return true;

        }

        return false;

    }

    public function getUserId($userEmail)
    {

        if($userEmail == null){

            return 12;
        }

        $usuario = $this->mapper->user(array('email'=>$userEmail))->fetch();

        if(!isset($usuario->id)){

            $user = new \stdClass;
            $user->email           = $userEmail;
            $user->departament     = 'dev';

            $this->mapper->user->persist($user);
            $this->mapper->flush();

            $usuario = $this->mapper->user(array('email'=>$userEmail))->fetch();

        }

        return $usuario->id;

    }

    public function getDateUpdate($data)
    {

        if($data->dateResolution > null)
        {
            return $data->dateResolution;

        }

        return $data->dateUpdate;

    }

    public function setStatus($jiraId,$data)
    {

        $ticket = new \stdClass;
        $ticket->jira_id    = $jiraId;
        $ticket->status     = $data->status;
        $ticket->date       = $this->getDateUpdate($data);

        $this->mapper->jira_status->persist($ticket);
        $this->mapper->flush();

    }

    public function getZendeskId($zendeskId)
    {

        $zendeskId = $this->mapper->ticket(array('ticket_id'=>$zendeskId))->fetch();

        return $zendeskId->id;

    }

    public function createData($data)
    {

        $zendeskId = ($data->ZendeskId > null)? $this->getZendeskId($data->ZendeskId) : null;

        $ticket = new \stdClass;
        $ticket->jira_id    = $data->jiraOn;
        $ticket->zendesk_id = $zendeskId;
        $ticket->user_id    = $this->getUserId($data->userAssignee);
        $ticket->name       = $data->title;

        $this->mapper->jira->persist($ticket);
        $this->mapper->flush();

        $getLastId = $this->mapper->jira()->fetch(Sql::orderBy('id')->desc()->limit(1));

        $this->setStatus($getLastId->id,$data);

    }

    public function updateData($data)
    {

        $getJiraId = $this->mapper->jira()->fetch(array('jira_id' => $data->jiraOn));

        $ticket = $this->mapper->jira[$getJiraId->id]->fetch();
        $ticket->jira_id    = $data->jiraOn;
        $ticket->zendesk_id = $data->ZendeskId;
        $ticket->user_id    = $this->getUserId($data->userAssignee);
        $ticket->name       = $data->title;

        $this->mapper->post->persist($ticket);
        $this->mapper->flush();

        $this->setStatus($getJiraId->id,$data);

    }

    public function setData($data)
    {

        if($this->checkData($data->jiraOn) == false)
        {

            $this->createData($data);

            return;

        }
        $this->updateData($data);

    }

    public function setJiraIssue($data)
    {

        $dateUpdate = $this->tratamentDate($data->updated);
        $dateResolution = $this->tratamentDate($data->resolutiondate);

        $On = $this->getOnId($data->watches->self);

        $objJiraData = (object) array(

            'jiraOn'            => $On,
            'title'             => $data->summary,
            'ZendeskId'         => $data->customfield_10300,
            'userAssignee'      => $data->assignee->emailAddress,
            'status'            => $data->status->name,
            'dateUpdate'        => $dateUpdate,
            'dateResolution'    => $dateResolution

        );

        $this->setData($objJiraData);

    }

    public function getData()
    {

        $getLastId = $this->mapper->jira()->fetch(Sql::orderBy('jira_id')->desc()->limit(1));

        if(isset($getLastId->jira_id)){

            $i = $getLastId->jira_id;

        }else{

            $i = 1;

        }

        $controller = $i+50;

        while ( $i <= $controller ) {

            $i++;

            $getData = $this->helper->getCurlJira($i);

            if(!isset($getData->errors)){

                $this->setJiraIssue($getData->fields);

            }

        }

        print_r('issures integradas com sucesso!');

    }

}