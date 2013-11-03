<?php
/**
 * Class Soundcloud_Track
 *
 * @author Marko Krüger <kontakt@marko-krueger.de>
 */
class Soundcloud_Track {

	protected $title;
	
	protected $uri;
	
	protected $id;
	
	protected $genre;
	
	protected $trackPermalink;
	
	protected $user;
	
	protected $userPermaLink;
	
	protected $options;
	/**
	 *
	 * @param SimpleXMLElement $track 
	 */
	public function __construct(SimpleXMLElement $track) {
		$this->setGenre($track->genre)
			 ->setId($track->id)
			 ->setUri($track->uri)
			 ->setTitle($track->title)
			 ->setTrackPermalink($track->{'permalink-url'})
			 ->setUserPermalink($track->user->{'permalink-url'})
			 ->setUser($track->user->username);		
	}
	
	public function setOptions($options) {
		$this->options = $options;
		return $this;
	}
	
	public function getOptions($url = true) {
		if($url) {
			$options = '';
			foreach($this->options as $option => $value) {
				$options .= '&' . $option . '=' . $value;
			}
			return $options;
		}
	}
	
	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function getUri() {
		return $this->uri;
	}

	public function setUri($uri) {
		$this->uri = $uri;
		return $this;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getGenre() {
		return $this->genre;
	}

	public function setGenre($genre) {
		$this->genre = $genre;
		return $this;
	}
	
	public function getTrackPermalink() {
		return $this->trackPermalink;
	}

	public function setTrackPermalink($permalink) {
		$this->trackPermalink = $permalink;
		return $this;
	}
	
	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	public function getUserPermalink() {
		return $this->userPermaLink;
	}

	public function setUserPermalink($user) {
		$this->userPermaLink = $user;
		return $this;
	}
}

// End of class Soundcloud_Track

