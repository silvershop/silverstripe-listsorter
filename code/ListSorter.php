<?php

/**
 * Control for front-end sorting manipulations
 */
class ListSorter extends ViewableData{

	private $request;
	private $sortoptions = array();
	private $current;
	private $sortkey;

	public function __construct(SS_HTTPRequest $request, $options = null) {
		$this->request = $request;
		if(is_array($options)){
			$this->setSortOptions($options);
		}
	}

	/**
	 * Replace all sort options with a new array 
	 * @param array $options
	 */
	public function setSortOptions($options) {
		$this->sortoptions = array();
		foreach($options as $key => $value) {
			if(is_numeric($key)){
				$key = $value;
			}
			if($value instanceof ListSorter_Option){
				$this->addSortOption($value);
			}else{
				$this->addSortOption(
					new ListSorter_Option($key, $value)
				);
			}
		}
	}

	/**
	 * Add sort option, and set according to sort request param.
	 * @param ListSorter_Option $option
	 */
	public function addSortOption(ListSorter_Option $option) {
		$this->sortoptions[(string)$option] = $option;
		$requestparam = $this->request->getVar('sort');
		if((string)$option === $requestparam){
			$this->current = $option;
		}
		if((string)$option->getReverseOption() === $requestparam){
			$this->current = $option->getReverseOption();
		}
	}

	/**
	 * Current sort option
	 * @return ListSorter_Option|null
	 */
	protected function getCurrentOption() {
		return $this->current;
	}

	protected function isCurrent(ListSorter_Option $option) {
		return $option === $this->getCurrentOption();
	}

	/**
	 * Get the available sorting options
	 */
	public function getSorts() {
		$sorts = new ArrayList();
		foreach($this->sortoptions as $option) {
			if($this->isCurrent($option)){
				if($option->isReversable()){
					$option = $option->getReverseOption();
				}
				$option = $option->customise(array(
					'IsCurrent' => true
				));
			}
			$sorts->push($option);
		}

		return $sorts;
	}

	/**
	 * Sort the given datalist with the current sort
	 */
	public function sortList($list) {
		if($current = $this->getCurrentOption()){
			$list = $list->sort($current->getSortSet());
		}

		return $list;
	}

}

/**
 * Encapsulate sort option title, sorting SQL,
 * GET parameter key, and reverse option.
 */
class ListSorter_Option extends ViewableData{

	protected $title;
	protected $id;
	protected $sortset;
	protected $reverseoption;

	function __construct($title, $sortset, ListSorter_Option $reverseoption = null) {
		$this->title = $title;
		$this->setID($title);
		$this->sortset = $sortset;
		if($reverseoption){
			$this->setReverseOption($reverseoption);
		}
	}

	function getTitle() {
		return $this->title;
	}

	function setTitle($title){
		$this->title = $title;
		return $this;
	}

	function getSortSet() {
		return $this->sortset;
	}

	function setReverseOption(ListSorter_Option $option) {
		$this->reverseoption = $option;
		if(!$option->isReversable()){
			if($this->getID() === $option->getID()){
				$option->setID((string)$option."_rev");
			}
			$option->setReverseOption($this);
		}

		return $this;
	}

	function getReverseOption() {
		return $this->reverseoption;
	}

	function isReversable() {
		return (bool)$this->reverseoption;
	}

	function setID($id) {
		$this->id = strtolower(trim($id));
	}

	function getID() {
		return $this->id;
	}

	function __toString() {
		return $this->id;
	}

	function getLink(){
		return $this->generateLink($this->getID());
	}

	/**
	 * Helper for creating sort links
	 */
	private function generateLink($id) {
		$url = Http::setGetVar('sort',$id,null,'&');
		//TODO: strip "start" pagination parameter,
			//as most users won't want to remain on paginated page when sorting
		return $url;
	}

}