<?php
class SeatGeak
{
    protected $id;
    protected $limit=10;
    protected $apiendpoint='https://api.seatgeek.com/2/events';
    protected $performerapiendpoint='https://api.seatgeek.com/2/performers';
    protected $url;
    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['limit'])) {
            $this->limit = $data['limit'];
        }
        $this->term=$this->correctName($data['term']);
    }

    public function getArtist() {
        $this->url=$this->apiendpoint."?performers.slug=".$this->term;
        return $this->callSeatGeak();
    }
    public function getEvents() {
        $this->url=$this->apiendpoint."?q=".$this->term;
        return $this->callSeatGeak();
    }
    public function getVenue() {
        $this->url=$this->apiendpoint."?q=".$this->term;
        return $this->callSeatGeak();
    }
    public function getPerformer() {
        $this->url=$this->apiendpoint."?performers.slug=".$this->term;
        return $this->callSeatGeak();
    }
    public function correctName($artist_name)
    {
        $artist_name = str_replace(" ", '-', $artist_name);
        $artist_name = str_replace("'s", ' ', $artist_name);    
        $artist_name = strtolower(trim($artist_name));
        
        return $artist_name;
    }

    public function callSeatGeak(){
       $useragent   = 'cURL';
       $headers     = false;
       $follow_redirects = false;
       $debug       = false;   
       $url = $this->url."&per_page=".$this->limit;
    # initialise the CURL library
       $ch = curl_init();

    # specify the URL to be retrieved
       curl_setopt($ch, CURLOPT_URL,$url);

    # we want to get the contents of the URL and store it in a variable
       curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    # specify the useragent: this is a required courtesy to site owners
       curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    # ignore SSL errors
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    # return headers as requested
       if ($headers==true){
        curl_setopt($ch, CURLOPT_HEADER,1);
    }

    # only return headers
    if ($headers=='headers only') {
        curl_setopt($ch, CURLOPT_NOBODY ,1);
    }

    # follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects==true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }

    # if debugging, return an array with CURL's debug info and the URL contents
    if ($debug==true) {
        $result['contents']=curl_exec($ch);
        $result['info']=curl_getinfo($ch);
    }else{ 
        $result=curl_exec($ch);
    }

    # free resources
    curl_close($ch);

    # send back the data    
    $result = json_decode($result, true); 
    if(isset($_GET['d'])){
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }  
    if( empty( $result['events'] )) {
        return -1;
    }else{
        return $result['events'];
    }
}
}