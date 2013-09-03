<?php

/**
 * Control for front-end sorting manipulations
 */
class ListSorter extends ViewableData{

	private $request;
	private $sortfields;

	function __construct(SS_HTTPRequest $request, $sortfields){
		$this->request = $request;
		$this->sortfields = $sortfields;
	}

	/**
	 * Current sort
	 */
	function getCurrent(){
		$sort = $this->request->getVar('sort');
		if(in_array($sort, $this->sortfields)){
			return $sort;
		}
		return null;
	}

	/*
	 * Current direction
	 */
	function getDirection(){
		$dir = $this->request->getVar('dir');
		return $dir;
	}

	/**
	 * Get the available sorting options
	 */
	function getSorts(){
		$sorts = new ArrayList();
		foreach($this->sortfields as $sort => $title){
			if(is_numeric($sort)){
				$sort = $title;
			}
			$iscurrent = ($this->getCurrent() == $sort);
			$dir = $iscurrent ? $this->oppositeDir($this->getDirection()) : "asc";
			$sorts->push(new ArrayData(array(
				'Title' => $title,
				'IsCurrent' => $iscurrent,
				'Link' => $this->generateLink($sort, $dir),
				'Direction' => $dir
			)));
		}
		return $sorts;
	}

	/**
	 * Sort the given datalist with the current sort
	 */
	function sortList($list){
		if($current = $this->getCurrent()){
			$list = $list->sort($current, $this->getDirection());
		}
		return $list;
	}

	/**
	 * Helper for creating sort links
	 */
	private function generateLink($field, $direction = null){
		$url = Http::setGetVar('sort',$field,null,'&');
		//TODO: strip "start" pagination parameter, as most users won't want to remain on page 23 when sorting
		if($direction == 'asc' || $direction == 'desc'){
			$url = Http::setGetVar('dir',$direction,$url,'&');
		}
		return $url;
	}

	private function oppositeDir($dir){
		switch($dir){
			case "asc":
				return "desc";
			case "desc":
				return "asc";
		}
		return "asc";
	}

}