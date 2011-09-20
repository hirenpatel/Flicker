<?php
class IndexController extends Zend_Controller_Action
{
 	private $flickrAPIKey = '033f82d2d67c628f650a643dc5ad8bac';
 	private $cache;
    public function init()
    {
        //init action
        $fc = Zend_Controller_Front::getInstance();
        $this->_baseUrl =  $fc->getBaseUrl();
        $this->view->baseUrl = $this->_baseUrl;
    }    
 
    public function indexAction()
    {	
    	//just load intial html    	
    }    
    
	public function photoAction()
    {	
    	$this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
    	
    	$boolShowPhotos = false;
        $searchTag = $this->_request->getParam('searchTag');       
        $html = '';
        if(empty($searchTag)) {
        	$html .= "Please enter search keyword";
        } else {        	
        	try {        		
        		$currentPage = 1;        		
        		$i=$this->_request->getParam('page');
        		$currentPage = (!empty($i)) ? $i : 1 ;        		

        	  	//initialize client with data set
			  	$client = new Zend_Rest_Client('http://api.flickr.com/services/rest/');
			  
			  	// set method name and API key
			  	$client->method('flickr.photos.search');
			  	$client->api_key($this->flickrAPIKey);
			  
			  	// set method arguments
			  	$client->tags($searchTag);			  
			  
			  	// perform request and process response			 
			    $result = $client->get();			  
        		
        		$photos = array();
        		if(isset($result)) {
        			foreach($result->photos->photo as $photo) {
        				$photos[] = array('thumbnail' => sprintf('http://farm%s.static.flickr.com/%s/%s_%s_t.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret']),
											'original' => sprintf('http://farm%s.static.flickr.com/%s/%s_%s_b.jpg', $photo['farm'], $photo['server'], $photo['id'], $photo['secret']),
        									'title' => $photo['title'],
        									'ownername'=>$photo['owner']);
        			}
        		}
        		$html .= "<table align='center' width='90%' cellpadding='2' cellspacing='2'>";
        		if(!empty($photos)) {        			
		            $paginator = Zend_Paginator::factory($photos);
		            $paginator->setItemCountPerPage(5);
		            $paginator->setPageRange(3);
		            $paginator->setCurrentPageNumber($currentPage);
		            Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');		            
		            $this->view->paginator = $paginator;	
		            	           
		            Zend_Paginator::setDefaultScrollingStyle('Sliding');
		            if($paginator) {  
        				$html .= "<tr><td colspan='5' align='center'>".$paginator."</td></tr>";
        				$html .= "<tr><td colspan='5' height='20'></td></tr>";
        				$html .= "<tr>";
        				$cnt = 1;
        				foreach($paginator as $photo) {
							$thumbnail = $photo['thumbnail'];							
							$title = $photo['title'];
							$img = (!empty($photo['original'])) ? $photo['original'] : '';
							$ownername = $photo['ownername'];							
							
							$html .= "<td width='20%' align='center'>";
							if(!empty($img)) {
								$html .= "<a href='".$img."' target='_blank'><img title='".$title."' class='thumb' src='".$thumbnail."'></a>";
								$html .= "<br><a title='".$ownername."' href='".$img."' target='_blank'>$ownername</a>";								
							} else {
								$html .= "<img class='thumb' src='".$thumbnail."' title='".$title."'>";
								$html .= "</br>".$ownername;
							}
							$html .= "<br></td>";							
							$cnt++;
						}	
						$html .= "</tr>";					
						$html .= "<tr><td colspan='5' height='20'></td></tr>";
        				$html .= "<tr><td colspan='5' align='center'>".$paginator."</td></tr>";
        				$html .= "</tr>";
		            }    				
        		} else { 
        			$html .= "<tr><td colspan='5'><br><br><font color='red'>Sorry, No photos available.</td></tr>"; 
        		}
        		$html .= "</table>";			

        	} catch (Exception $e) { 
        		$html .= ('ERROR: ' . $e->getMessage()); 
        	}
        	echo $html;
        }
    }
}