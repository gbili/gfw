<?php
namespace Gbili\VLs\Req;

use Gbili\Db\Req\Admin as RealAdmin;

/**
 * 
 * @author gui
 *
 */
class Admin
extends \Gbili\VLs\Req
{
	/**
	 * 
	 * @param unknown_type $differentPrefixedAdapter
	 * @return unknown_type
	 */
	public function __construct($differentPrefixedAdapter = null)
	{
		parent::__construct($differentPrefixedAdapter);
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function updateCatsWithThumbsTable()
	{	
		$rA = new RealAdmin();
		$rA->emptyTables('Gbili_Vid_CatWithThumb');
		
		$c = APP_IPP;
		return $this->getResultSet("INSERT INTO Gbili_Vid_CatWithThumb (viewCount, vidsCount, imageLocalUrl, catName, catSlug, vidId, categoryId) 
											(SELECT vwcnt.viewCount AS viewCount, 
													vdcnt.vidsCount AS vidsCount, 
													i.localUrl AS imageLocalUrl, 
													vc.value AS catName, 
													vc.slug AS catSlug, 
													v.vidId AS vidId,
													v.categoryId AS categoryId
												FROM (SELECT categoryId, 
											 			 	 MAX(viewCount) AS viewCount
											 		 	FROM Vid 
											 			GROUP BY categoryId) AS vwcnt
											 		INNER JOIN (SELECT categoryId,
											 					   	   COUNT(*) AS vidsCount 
											 					 FROM Vid 
											 				 	 GROUP BY categoryId) AS vdcnt ON (vwcnt.categoryId = vdcnt.categoryId)  
											 		INNER JOIN Vid AS v ON (vwcnt.categoryId = v.categoryId) 
											 		INNER JOIN Vid_Category AS vc ON (vwcnt.categoryId = vc.elementId) 
											 		INNER JOIN Vid_Image AS i ON (v.imageId = i.imageId) 
											 	WHERE v.viewCount = vwcnt.viewCount 
											 	GROUP BY v.categoryId
											 	ORDER BY vc.viewCount DESC LIMIT 0, $c)");
	}
	
	/**
	 * 
	 * @param unknown_type $e
	 * @return unknown_type
	 */
	public function logException($e)
	{
		if ($e instanceof \Gbili\Exception\Exception) {
			$e = $e->__toString();
		}
		$this->insertUpdateData('INSERT INTO Gbili_Exception (value) VALUES (:value)', array(':value' => $e));
	}
}