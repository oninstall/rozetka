<?php
/**
* Rozetka
* 
* Sends the url to parse, analyze html page and generates data for the base.
* 
* @author Patenko patenkoss@gmail.com
* check Name3
*/
class Rozetka {
    /**
     * @var $pagesLimit is limit of pages for parsing.
	 * @var $searchURL is url to parse.
	 * @var $request to create an object of class Request.
	 * @var $model to create an object of class Model.
     */
    public $pagesLimit = 20;
    private $searchURL = URL_QUERY;
    private $request;
    private $model;
    /**
     * This function is for getting list of Rozetka's items by
     * keyword.
     *
     * First we walk throw pages and get all items from pages
     * while page exist. Next we sort it by  price, create a 
	 * variable in CSV format with items and put that data to
	 * database.
     *
     * @param string $keyword is string for searching at Rozetka.
     */
    public function parse($keyword){
		$this->searchURL .= 'search/?text={text}&p={page}';
		$URL = str_replace("{text}", urlencode($keyword), $this->searchURL);
        $resultItems = array();
        $pageNum = 0;
		$this->request = new Request();
        while ($pageNum < $this->pagesLimit){
            $currentURL = str_replace('{page}', $pageNum, $URL);
			echo $currentURL;
            $pageNum++;
            $htmlpage = $this->request->make_request($currentURL);
            if ( ! $htmlpage){
                // This is a sign that it was the last page.
                if ($this->request->lastHTTPCode == 404 ||
                        $this->request->lastHTTPCode == 301){
                    echo 'All pages parsed.';
                    break;
                }
                if ($this->request->lastErrorCode){
                    echo 'ERROR CODE: '.$this->request->lastErrorCode;
                    return;
                }
				if ($this->request->lastHTTPCode != 200){
                    echo 'HTTP CODE: '.$this->request->lastHTTPCode;
                    return;
                }
            }
            $items = $this->parse_items_list($htmlpage);
            if (is_array($items)){
                echo 'Parsed '.count($items).' items from page '.$pageNum."\n";
                /**
                 * Save everything in a single array.
                 */
                $resultItems = array_merge($resultItems, $items);
            } else {
                break;
            }
        }
        /**
         * Now we have array $resultItems with all items from search.
		 * sorting by price.
		 * price is array to sort. 
         */
		 foreach ($resultItems as $key => $row) {
			$price[$key]  = $row['price'];
		}
		array_multisort($price, SORT_ASC, SORT_NUMERIC, $resultItems);

        /**
         * $csvItems is string with items format csv.
         */
		$csvItems = null;
        foreach($resultItems as $item){
		$this->csvItems.= $this->array_to_csv($item);
		}
		/**
        * Add items to database.
        */
		$this->model = new Model();
        $this->model->add_item($keyword, $this->csvItems);
        return true;
    }
    /**
     * Converts an array into a single CSV line
     *
     * @param array $array.
	 * @return string $csv_string.
     */
	function array_to_csv($array){
		$csv_arr = array();
		foreach ($array as $value){
			$csv_arr[]='"'.preg_replace('/"/','""',$value).'"';
		}
		$csv_string=implode(',',$csv_arr);
		$csv_string.="\r\n";
		return $csv_string; 
	}
    /**
     * Using regular expressions to find and analyze needed fields.
     *
     * @param string $html Variable containing html code of current search results page.
	 * @return array $onPageItems is array of items per page.
     */
    protected function parse_items_list($html){

        $onPageItems = array();
		$currentItem = array();
        $regexpName = '/\<td\ class\=\"detail\"\>[\r\n\t]*\<div\ class\=\"title\"\>';
		$regexpName .= '[\r\n\t]*\<a\ href\=\"(.*)\"\>(.*)\<\/a\>/smU';
        $regexpPrice = '/\<div\ class\=\"uah\"\>(.*)\<\/span\>/smU';
        /*
        * $matchesName[1] is link.
        * $matchesName[2] is name.
		* $matchesPrice[1] is price.
        */
        if (preg_match_all($regexpName, $html, $matchesName)){  
			preg_match_all($regexpPrice, $html, $matchesPrice);
			for($i=0; $i<count($matchesName[0]); $i++){
				$currentItem['name'] = trim($matchesName[2][$i]);
				$urlParts = explode('/', trim($matchesName[1][$i]));
				$model = ucfirst(str_replace('_', ' ', $urlParts[3]));
				// Not every url contains model name. In this case 'ru' string is on
				// 3rd position.
				$model = ($model === 'ru') ? $currentItem['name'] : $model;
				$currentItem['model'] = $model;
				$currentItem['price'] = trim($matchesPrice[1][$i]);
				$currentItem['link'] = trim($matchesName[1][$i]);
				// Collect items on page to $onPageItems.
				array_push($onPageItems, $currentItem);
			}
		}
		return $onPageItems;
	}
}
?>