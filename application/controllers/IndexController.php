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
        		$flickr = new Zend_Service_Flickr($this->flickrAPIKey);
        		$currentPage = 1;        		
        		$i=$this->_request->getParam('page');
        		$currentPage = (!empty($i)) ? $i : 1 ;        		
        		$options = array(
					        'per_page' => '30', 
					        'page'     => $currentPage,
					        'tag_mode' => 'all',
					        );
        		$photosFlickr = $flickr->tagSearch($searchTag,$options);
        		$photos = array();
        		if($photosFlickr->totalResultsAvailable > 0) {
        			foreach($photosFlickr as $photosData) {
        				$photos[] = array('thumbnail' => (!empty($photosData->Square->uri)) ? $photosData->Square->uri : '',
											'original' => (!empty($photosData->Original->uri)) ? $photosData->Original->uri : '',
        									'large' => (!empty($photosData->Large->uri)) ? $photosData->Large->uri : '',
        									'medium' => (!empty($photosData->Medium->uri)) ? $photosData->Medium->uri : '',
											'title' => $photosData->title,
        									'ownername'=>$photosData->ownername);
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
		            	           
		            /**
		             * We will be using $this->view->paginator to loop thru in our view ;-)
		             */		 
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
							$img = (empty($img)) && (!empty($photo['large'])) ? $photo['large'] : '';
							$img = (empty($img)) && (!empty($photo['medium'])) ? $photo['medium'] : '';
							$ownername = $photo['ownername'];
							
							
							$html .= "<td width='20%' align='center'>";
							if(!empty($img)) {
								$html .= "<a href='".$img."' target='_blank'><img title='".$img."' class='thumb' src='".$thumbnail."'></a>";
								$html .= "<br><a title='".$ownername."' href='".$img."' target='_blank'>$ownername</a>";								
							} else {
								$html .= "<img class='thumb' src='".$thumbnail."'>";
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
        		$html .=  $e; 
        	}
        	echo $html;
        }
    }
}