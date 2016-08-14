<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
 ini_set('display_errors','On');
require 'vendor/autoload.php';
spl_autoload_register(function ($classname) {
	require ("classes/" . $classname . ".php");
});
require_once 'config.php';
$config['displayErrorDetails'] = true;
$config['BASEURL'] = BASEURL;
$config['AFFILIATEID'] = AFFILIATEID;
$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer("templates/");
$app->get('/getscript', function (Request $request, Response $response) {
	$base = $this->get('settings')['BASEURL'];
	$response = $this->view->render($response, "script.phtml", ["base" => $base, "router" => $this->router]);
	return $response;
});
$app->get('/api/artist', function (Request $request, Response $response) {
	$data=array(
		'term'=>$request->getParam('term'),
		'limit'=>$request->getParam('count')
		);
	$seatgeak = new SeatGeak($data);
	$affiliateID = $this->get('settings')['AFFILIATEID'];
	$artists = $seatgeak->getArtist();
	// echo "<pre>";
	// print_r($artists);
	// echo "</pre>";
	$response = $this->view->render($response, "artists.phtml", ["affiliateID"=>$affiliateID,"artists" => $artists, "router" => $this->router]);
	return $response;
});
$app->get('/api/venue', function (Request $request, Response $response) {
	$data=array(
		'term'=>$request->getParam('term'),
		'limit'=>$request->getParam('count')
		);
	$affiliateID = $this->get('settings')['AFFILIATEID'];
	$seatgeak = new SeatGeak($data);
	$venues = $seatgeak->getVenue();
	// echo "<pre>";
	// print_r($venues);
	// echo "</pre>";
	$response = $this->view->render($response, "venues.phtml", ["affiliateID"=>$affiliateID,"venues" => $venues, "router" => $this->router]);
	return $response;
});
$app->get('/api/event', function (Request $request, Response $response) {
	$data=array(
		'term'=>$request->getParam('term'),
		'limit'=>$request->getParam('count')
		);
	$affiliateID = $this->get('settings')['AFFILIATEID'];
	$seatgeak = new SeatGeak($data);
	$events = $seatgeak->getEvents();
	// echo "<pre>";
	// print_r($events);
	// echo "</pre>";
	$response = $this->view->render($response, "events.phtml", ["affiliateID"=>$affiliateID,"events" => $events, "router" => $this->router]);
	return $response;
});
$app->get('/api/category', function (Request $request, Response $response) {
	$data=array(
		'term'=>$request->getParam('term'),
		'limit'=>$request->getParam('count')
		);
	$affiliateID = $this->get('settings')['AFFILIATEID'];
	$seatgeak = new SeatGeak($data);
	$events = $seatgeak->getEvents();
	// echo "<pre>";
	// print_r($events);
	// echo "</pre>";
	$response = $this->view->render($response, "category.phtml", ["affiliateID"=>$affiliateID,"events" => $events, "router" => $this->router]);
	return $response;
});
$app->get('/ticket/new', function (Request $request, Response $response) {
	$component_mapper = new ComponentMapper($this->db);
	$components = $component_mapper->getComponents();
	$response = $this->view->render($response, "ticketadd.phtml", ["components" => $components]);
	return $response;
});
$app->post('/ticket/new', function (Request $request, Response $response) {
	$data = $request->getParsedBody();
	$ticket_data = [];
	$ticket_data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
	$ticket_data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);
    // work out the component
	$component_id = (int)$data['component'];
	$component_mapper = new ComponentMapper($this->db);
	$component = $component_mapper->getComponentById($component_id);
	$ticket_data['component'] = $component->getName();
	$ticket = new TicketEntity($ticket_data);
	$ticket_mapper = new TicketMapper($this->db);
	$ticket_mapper->save($ticket);
	$response = $response->withRedirect("/tickets");
	return $response;
});
$app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
	$ticket_id = (int)$args['id'];
	$mapper = new TicketMapper($this->db);
	$ticket = $mapper->getTicketById($ticket_id);
	$response = $this->view->render($response, "ticketdetail.phtml", ["ticket" => $ticket]);
	return $response;
})->setName('ticket-detail');
$app->run();