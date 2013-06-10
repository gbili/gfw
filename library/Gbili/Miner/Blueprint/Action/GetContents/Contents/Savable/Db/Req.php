<?php
namespace Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable\Db;

use Gbili\Miner\Blueprint\Action\GetContents\Contents\Savable as ContentsSavable;
use Gbili\Db\Req\AbstractReq;
use Gbili\Url\Url;

class Req
extends AbstractReq
{
    
    protected $tableName = 'Content';
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * This will get the paths an new instance
	 * generating point action id
	 * 
	 * @param Host $host
	 * @return unknown_type
	 */
	public function getContents(Url $url)
	{
		$res = $this->getResultSet("SELECT c.url     as url,
                                		   c.content as contents,
                                    	   c.cId    as contentsId
									FROM {$this->tableName} AS c
									WHERE c.url = :url",
								  array(':url' => $url->toString()));
		if (is_array($res)) {
		    $row = current($res);
		    if (isset($row['contents'])) {
		        return $row['contents'];
		    }
		}
	    return false;
	}
	
	/**
	 * 
	 * @param Url $url
	 * @param unknown_type $contents
	 */
	protected function insertContent(Url $url, $contents)
	{
	    return $this->insertUpdateData("INSERT INTO {$this->tableName} (url, content) VALUES (?, ?)", array($url->toString(), $contents));
	}
	
	/**
	 * 
	 * @param Url $url
	 * @param unknown_type $returnId
	 */
	protected function existsContent(Url $url, $returnId = false)
	{
	    return $this->existsElement("SELECT c.cId as id 
                        	             FROM {$this->tableName} AS c
                            	      WHERE c.url = ?", 
                                	  array($url->toString()),
                            	     'id', 
                            	     (boolean) $returnId);
	}
	
	/**
	 * 
	 * @param ContentsSavable $c
	 * @return boolean
	 */
	public function save(ContentsSavable $c)
	{
	    if (false !== $id = $this->existsContent($c->getUrl(), true)) {
            $c->setId($id);
	        return;
	    }
	    $contents = $c->getContents();
	    $this->insertContent($c->getUrl(), $c->getContents());
	    $id = $this->getAdapter()->lastInsertId();
	    $c->setId($id);
	}
}