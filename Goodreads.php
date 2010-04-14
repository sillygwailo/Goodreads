<?php

/**
 * @file
 * PHP5 Library implementing the Goodreads API
 * @todo functions for the write API
 * @todo functions for URLs that don't fit well with the execute() 
 *  function parameters
 * @todo select license
 * @todo document each function with parameters and returns
 * @see http://www.goodreads.com/api
 */
/*
 * Implements the Goodreads API.
 *
 */
class Goodreads {

  /**
   * Constructor
   *
   * @param $key
   *  Goodreads API key
   * @param $secret
   *  Goodreads secret key (required for the write API)
   *
  */
  public function __construct($key, $secret = '') {
    $this->key = $key;
    $this->secret = $secret;
    $this->goodreads = 'http://www.goodreads.com/';
  }

  protected function _get_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $execution = array();
    $execution['content'] = curl_multi_getcontent($ch);
    $content = simplexml_load_string($execution['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
    return $content;
  }

  protected function _build_query($options = array()) {
    if (empty($options['format'])) {
      $options['format'] = 'xml';
    }

    $options['key'] = $this->key;

    // construct the query
    $queries = array();

    return http_build_query($options);
  }

  protected function _execute_oauth($method, $options) {

  }

  protected function _execute_override($method) {
    $query = '?key=' . $this->key;
    $url = $this->goodreads . $method . $query;
    return $this->_get_curl($url);
  }

  protected function _execute($method, $options = array()) {
    if (empty($options['format'])) {
      $options['format'] = 'xml';
    }

    $options['key'] = $this->key;
    
    $query = $this->_build_query($options);

    $url = $this->goodreads . $method . '?' . $query;

    return $this->_get_curl($url);
  }

  /** 
   * Get id of user who authorized OAuth.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function auth_user() {

  }

  /** 
   * Paginate an author's books.
   *
   * @param $id
   *  Author ID.
   * @param $page
   *  (Optional) Which page of listings.
   * @return array
   *  $books An array of objects with information about the author's books.
   *  
   */
  public function author_books($id, $page = 1) {
    $options = array();
    $options['id'] = $id;
    $options['page'] = $page;
    $result = $this->_execute('author/list', $options);
    $books = $result->author->books;
    $books = get_object_vars($books);
    return $books['book'];
  }

  /** 
   * Get info about an author by id.
   *
   * @param $id
   *  Goodreads Author ID.
   * @return object
   *  An object with the author's information, with up to 10 books in the 
   *  $author->books object.
   */
  public function author_show($id) {
    $options = array();
    $options['id'] = $id;
    $result = $this->_execute('author/show', $options);
    return $result->author;
  }

  /** 
   * Get the reviews for a book given a Goodreads book id.
   *
   * @param $id
   *  The book's Goodreads ID.
   * @param $page
   *  (Optional) Which page of listings.
   * @return object
   *  An object with the author's information, with up to 10 books in the 
   *  $author->books object.
   *
   */
  public function book_show($id, $page = 1) {
    $options = array();
    $options['id'] = $id;
    $options['page'] = $page;
    $result = $this->_execute('book/show', $options);
    return $result->book;
  }

  /** 
   * Get the reviews for a book given an ISBN.
   *
   * @param $isbn
   *  ISBN for the book.
   * @param $page
   *  The page number of reviews.
   * @return object
   *  An object with the author's information, with up to 10 books in the 
   *  $author->books object.
   *
   */
  public function book_show_by_isbn($isbn, $page = 1) {
    $options = array();
    $options['isbn'] = $isbn;
    $options['page'] = $page;
    $result = $this->_execute('book/isbn', $options);
    return $result->book;
  }

  /** 
   * Get the reviews for a book given a title string.
   *
   * @param $title
   * @param $page
   * @return object
   *  An object with the book's information, with up to 10 reviews in the
   *  $book->reviews object.
   *  
   */
  public function book_show_by_title($title, $page = 1) {
    $options = array();
    $options['title'] = $title;
    $options['page'] = $page;
    $result = $this->_execute('book/title', $options);
    return $result->book;
  }

  /** 
   * Create a comment.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function comment_create() {

  }

  /** 
   * List comments on a subject.
   *
   * @todo Find data to test this on, currently doesn't work.
   * @param $id 
   *  ID of the resources, specified by the $type parameter.
   * @param $type
   *  One of 'photo', 'poll', 'list', 'author_blog_post',
   *  'blog', 'quiz', 'librarian_note', 'interview', 'review', 
   *  'topic', 'owned_book', 'user_list_vote', 'recommendation', 
   *  'video', 'user_status', 'question', 'user'.
   * @param $page
   *  The page number of reviews.
   * @return
   *  
   */
  public function comment_list($id, $type, $page = 1) {
    $possible_types = array(
      'photo', 
      'poll', 
      'list', 
      'author_blog_post', 
      'blog', 
      'quiz', 
      'librarian_note',
      'interview',
      'review',
      'topic',
      'owned_book',
      'user_list_vote',
      'recommendation',
      'video',
      'user_status',
      'question',
      'user',
    );
    if (in_array($type, $possible_types)) {
      $options = array();
      $options['id'] = $id;
      $options['type'] = $type;
      $options['page'] = $page;
      $result = $this->_execute('comment', $options);
      return $result; 
    }
    else {
      // return an error message.
    }
      
  }

  /** 
   * Events in your area.
   *
   * @param $lat
   *  Latitude.
   * @param $lng
   *  Longitude.
   * @return
   *  A list of events.
   * @todo Better define the return in the documentation.
   */
  public function events_list($lat, $lng) {
    $options = array();
    $options['lat'] = $lat;
    $options['lng'] = $lng;
    $result = $this->_execute('event', $options);
    return $result->events;
  }

  /** 
   * Get the books from a listopia list.
   *
   * @param $id
   *  Goodreads ID for the list.
   * @return array
   *  An array of objects, each with information about the book.
   *  
   */
  public function list_show($id) {
    $result = $this->_execute_override("list/show/$id.xml");
    $list = $result->list;
    $books = $list->books;
    $book_list = get_object_vars($books);
    return $book_list['book'];
  }

  /** 
   * Get the listopia lists for a given tag.
   *
   * @param $name
   *  Name of the tag to show listopia lists for.
   * @return array
   *  An array of objects containing information about the lists.
   *  
   */
  public function list_show_tag($name) {
    $options = array();
    $options['name'] = $name;
    $result = $this->_execute('list/show_tag', $options);
    $lists = $result->lists;
    $list = get_object_vars($lists);
    return $list['list'];
  }

  /** 
   * Add a quote.
   *
   * @param $body
   *  Body text of the quote. (required)
   * @param $author
   *  Name of the quote author (required)
   * @param $options
   *  Array of optional parameters.
   *  - book_id: Goodreads ID of the book from which the quote was taken
   *  - author_id: Goodreads ID of the author.
   *  - tags: Comma-separated tags
   *  - isbn: ISBN of the book from which the quote was taken. This will not override the book_id if it was provided.
   * @return boolean
   *  TRUE if successful.
   *  
   */
  public function quotes_create($body, $author, $options = array()) {
    $options['quote[body]'] = $body;
    $options['quote[author_name]'] = $author;
    if (!empty($options)) {
      foreach (array_keys($options) as $quote_key) {
        $options["quote[$quote_key]"] = isset($options[$quote_key]) ? $options[$quote_key]: NULL;
      }
    }
    $result = $this->_execute_oauth("quotes.xml", $options);
  }

  /** 
   * Add review.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function review_create() {

  }

  /** 
   * Destroy a review.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function review_destroy() {

  }

  /** 
   * Get the books on a members shelf.
   *
   * @todo Implement search[query]
   * @param $id
   *  Goodreads ID of the user to list reviews for.
   * @param $options
   *  An array of options with the following optional keys:
   *  - sort: one of 'available_for_swap', 'position', 'votes', 
   *    'rating', 'shelves', 'avg_rating', 'isbn', 'comments', 'author', 
   *    'title', 'notes', 'cover', 'review', 'condition', 'date_started',
   *    'random', 'date_read', 'year_pub', 'date_added', 'date_purchased', 
   *    'num_ratings', 'purchase_location', or 'date_updated'
   *  - page: which page of results to show (default 1)
   *  - per_page: how many reviews to show per page (default 10)
   *  - order: a for ascending, d for descending (default d)
   * @return array
   *  Array of objects with information about each review.
   *  
   */
  public function reviews_list($id, $options = array()) {
    $possible_sort = array(
      'available_for_swap',
      'position',
      'votes',
      'rating',
      'shelves',
      'avg_rating',
      'isbn',
      'comments',
      'author',
      'title',
      'notes',
      'cover',
      'review',
      'condition',
      'date_started',
      'random',
      'date_read',
      'year_pub',
      'date_added',
      'date_purchased',
      'num_ratings',
      'purchase_location',
      'date_updated',
    );
    if (isset($options['sort']) && in_array($options['sort'], $possible_sort)) {
      $options['sort'] = $sort;
    }
    else {
      unset($options['sort']);
    }
    if (!isset($options['per_page'])) $options['per_page'] = 10;
    if (!isset($options['page'])) $options['page'] = 1;
    if (!isset($options['order'])) $options['order'] = 'a';
    $options['v'] = 2; 
    $options['id'] = $id;
//    if ($search != NULL) {
//      $options['search[]']
//    }
    $result = $this->_execute('review/list', $options);
    $reviews = $result->reviews;
    $reviews = get_object_vars($reviews);
    return $reviews['review'];
  }

  /** 
   * Recent reviews from all members.
   *
   * No parameters.
   *
   * @return array
   *  Array of objects with information about the recent reviews.
   *  
   */
  public function review_recent_reviews($options = array()) {
    $result = $this->_execute('review/recent_reviews', $options);
    $reviews = $result->reviews;
    $reviews = get_object_vars($reviews);
    return $reviews['review'];
  }

  /** 
   * Get a review.
   *
   * @param $id
   *  Goodreads ID of the review to show.
   * @param $page
   * (Optional) Page number of the comments (default 1).
   * @return object
   *  An object containing information about the review.
   *  
   */
  public function review_show($id, $page = 1) {
    $options = array();
    $options['id'] = $id;
    $options['page'] = $page;
    $result = $this->_execute('review/show', $options);
    return $result->review;
  }

  /** 
   * Get a user's review for a given book.
   *
   * @param $user_id
   *  The Goodreads ID for the user.
   * @param $book_id
   *  The Goodreads ID for the book.
   * @return object
   *  An object containing information about that user's review.
   */
  public function review_show_by_user_and_book($user_id, $book_id) {
    $options = array();
    $options['user_id'] = $user_id;
    $options['book_id'] = $book_id;
    $result = $this->_execute('review/show_by_user_and_book', $options);
    return $result->review;
  }

  /** 
   * Update book reviews.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function review_update() {

  }

  /** 
   * Find an author by name.
   *
   * @todo Needs to override the URL
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function search_authors() {

  }

  /** 
   * Find books by title, author, or ISBN.
   *
   * @param $q
   *  The query text to match against book title, author, and isbn fields. Supports boolean operators and phrase searching.
   * @param $field
   *  The field to search. One of 'all', 'title', 'author', 'isbn' or 'genre'.
   * @param $page
   *  Which page of results to return (default 1).
   * @return array
   *  An array of objects with information about the results.
   *  
   */
  public function search_books($q, $field = 'all', $page = 1) {
    $possible_fields = array(
      'all',
      'title',
      'author', 
      'isbn', // undocumented
      'genre',
    );
    $options = array();
    if (in_array($field, $possible_fields)) {
      $options['search[field]'] = $field;
    }
    else {
      $options['search[field]'] = 'all';
    }
    $options['q'] = $q;
    $options['page'] = $page;
    $result = $this->_execute('search/search', $options);
    $search = $result->search->results;
    $search = get_object_vars($search);
    return $search['work']; 
  }

  /** 
   * Add a book to a shelf.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function shelves_add_to_shelf() {

  }

  /** 
   * Add book shelf.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function shelves_create() {

  }
  
  /** 
   * Get a user's shelves.
   *
   * @param $user_id
   *  Goodreads user ID.
   * @param $page
   *  Page of results to display (default 1)
   * @return array
   *  An array of objects with information about the user's shelves.
   */
  public function shelves_list($user_id, $page = 1) {
    $options = array();
    $options['user_id'] = $user_id;
    $options['page'] = $page;
    $result = $this->_execute('shelf/list', $options);
    $shelves = $result->shelves;
    $shelves = get_object_vars($shelves);
    return $shelves['user_shelf'];
  }

  /** 
   * Get your friend updates.
   *
   * @todo Requires app registration.
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function updates_friends() {

  }

  /** 
   * Get info about a member by id or username.
   *
   * @todo Test whether 'id' is optional, then it can pass username or ID.
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function user_show($user) {
    
  }

  /** 
   * Get a user's friends.
   *
   * @todo Check if user is authenticated using OAuth, then $id is optional.
   * @param $id
   *  Goodreads user ID. 
   * @param $sort
   *  One of 'last_online' (default), 'date_added', 'first_name'.
   * @param $page
   *  Which page of results (default 1)
   * @return array
   *  An array of objects with information about the users. If only one
   *  result, then it returns an object with that result.
   */
  public function user_friends($id, $sort = 'last_online', $page = 1) {
    $possible_sorts = array(
      'last_online',
      'date_added',
      'first_name',
    );
    $options = array();
    if (in_array($sort, $possible_sorts)) {
      $options['sort'] = $sort;
    }
    else {
      $options['sort'] = 'last_online';
    }
    $options['id'] = $id;
    $options['page'] = $page;
    $result = $this->_execute('friend/user', $options);
    $friends = $result->friends;
    $friends = get_object_vars($friends);
    return $friends['user'];
  }

  /** 
   * Get a user's followers.
   *
   * Required authentication. Unimplemented.
   *
   * @todo Implement $page paramater.
   * @todo Authentication.
   * @param $id
   *  Goodreads user ID.
   * @param $page
   *  Page of results (default 1). Unimplemented.
   * @return
   *  
   */
  public function user_followers($id, $page = 1) {
    $result = $this->_execute_override("user/$id/followers.xml");
    return $result;
  }

  /** 
   * Get people a user is following.
   *
   * Requires authentication. Unimplemented.
   *
   * @todo Authentication
   * @param $id
   *  Goodreads user ID
   * @param $options
   *  (Optional) Array of options. Defaults to XML to account for URL format.
   * @return
   *  Unimplemented.
   */
  public function user_following($id, $options = array('format' => 'xml')) {
      $result = $this->_execute("user/$id/following", $options);
      return $result;
  }

  /** 
   * Update user status.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function user_status_create() {

  }

  /** 
   * Delete user status.
   *
   * @param 
   * @param 
   * @param 
   * @return
   *  
   */
  public function user_status_destroy() {

  }

}