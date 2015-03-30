<?php
namespace Gbili\Tests\Miner\Blueprint\Db;

class Req
implements \Gbili\Miner\Blueprint\Db\DbInterface
{
	/**
	 * This will get the paths an new instance
	 * generating point action id
	 * 
	 * @param \Gbili\Url\Authority\Host $host
	 * @return unknown_type
	 */
	public function getBlueprintInfo(\Gbili\Url\Authority\Host $host)
	{
        return array (
                    0 => 
                    array (
                    'bId' => '5',
                    'newInstanceGeneratingPointActionId' => '19',
                    ),
                    );
                    //$this->getResultSet("SELECT b.bId AS bId,
        //					   b.newInstanceGeneratingPointActionId AS newInstanceGeneratingPointActionId,
            //					FROM Blueprint AS b 
            //					WHERE b.host = :host",
            //                  array(':host' => $host->toString()));
	}
	
	/**
	 * 
	 * @param unknown_type $injectedActionId
	 * @return unknown_type
	 */
	public function getInjectionData($injectedActionId)
	{
        if (in_array((integer)$injectedActionId, range(6,19))) {
            return array();
        }
		//$this->getResultSet("SELECT b.bActionId AS injectingActionId,
	//									   b.inputGroup AS inputGroup 
	//								FROM BAction_r_InjectedBAction AS b 
	//								WHERE b.injectedActionId = :id",
	//								array(':id' => (integer) $injectedActionId));
	}
	
	/**
	 * For the actions of type extract, the result is returned
	 * as an indexed array with the group number as key and the
	 * result as value.
	 * For that type of array results, this function will return
	 * an array mapping the group number in result to the name
	 * of the entity.
	 * Ex : extract result : array(0=>'whole group', 1=>'Big lebowsky', 2=>'johny depp')
	 * 		mapping : array(1=>'Title', 2=>'Actor')
	 * then the two arrays should be combined to get an array like
	 * 		final : array('Title'=>'Big Lebowsky', 'Actor'=>'Johnny Depp')
	 * but this is done from blueprint, not from here.
	 * This returns the mapping array.
	 * 
	 * @param integer $actionId the id of the action in Db
	 * @return array
	 */
	public function getActionGroupToEntityMapping($actionId)
	{
        if (in_array((integer)$actionId, array(7, 8, 11))) {
            return array();
        } else if ((integer)$actionId === 19) {
            return array (
                    0 => array (
                            'regexGroup' => 'tumbnailAlt',
                            'entity' => '21',
                            'isOpt' => '0',
                        ),
                    1 => array (
                            'regexGroup' => 'tumbnailSrc',
                            'entity' => '22',
                            'isOpt' => '0',
                        ),
                    ); 
        } else if ((integer)$actionId === 9) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'name',
                    'entity' => '23',
                    'isOpt' => '0',
                    ),
                    1 => 
                    array (
                    'regexGroup' => 'productUrl',
                    'entity' => '11',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 12) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productImgBigSrc',
                    'entity' => '12',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 13) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productImgAlt',
                    'entity' => '13',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 14) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productImgMedSrc',
                    'entity' => '14',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 15) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productTitle',
                    'entity' => '24',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 16) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productPrice',
                    'entity' => '15',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 17) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productSelectedWeight',
                    'entity' => '16',
                    'isOpt' => '0',
                    ),
                    1 => 
                    array (
                    'regexGroup' => 'productSelectedWeightUnit',
                    'entity' => '1',
                    'isOpt' => '0',
                    ),
                    );
        } else if ((integer)$actionId === 18) {
            return array (
                    0 => 
                    array (
                    'regexGroup' => 'productDescription',
                    'entity' => '2',
                    'isOpt' => '0',
                    ),
                    );
        }
        throw new Exception('out of range');
		$sql = "SELECT b.regexGroup AS regexGroup, 
					   b.const AS entity,
					   b.isOpt AS isOpt
					FROM BAction_RegexGroup_r_Const AS b
					WHERE b.bActionId = :actionId";
		return $this->getResultSet($sql, array(':actionId' => $actionId));
	}

	/**
	 * Returns all the rows in tha Actions table
	 * where the host is the same as specified
	 * in the url
	 * 
	 * @param \Gbili\Url\Authority\Host $host
	 * @return unknown_type
	 */
	public function getActionSet(\Gbili\Url\Authority\Host $host)
	{
        if ($host->toString() !== 'shopstarbuzz.com') {
            throw Exception('expecting shopstarbuzz');
        }
        return array (
            0 => array (
                'actionId' => '6',
                'parentId' => '6',
                'inputGroup' => '0',
                'type' => '13',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '1.1. Go to products page',
                'data' => 'http://www.shopstarbuzz.com/starbuzz/?sort=featured&page=1',
                ),
            1 => array (
                'actionId' => '7',
                'parentId' => '6',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.1. Extract products list from page',
                'data' => '<ul class="ProductList[^>]+?>.+?</ul>',
                ),
            2 => array (
                'actionId' => '8',
                'parentId' => '7',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '1',
                'isOpt' => '0',
                'title' => 'Get each product list item',
                'data' => '<li class=".+?</li>',
                ),
            3 => array (
                'actionId' => '19',
                'parentId' => '8',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => 'extract each product thumbnail',
                'data' => '<img src="(?P<thumbnailSrc>[^"]+?)" alt="(?P<thumbnailAlt>[^"]+?)"',
                ),
            4 => array (
                'actionId' => '9',
                'parentId' => '8',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => 'extract each product details url and name',
                'data' => '<div class="ProductDetails">[^<]+?<strong><a href="(?P<productUrl>[^"]+?)" class="">(?P<name>[^<]+)</a></strong>',
                ),
            5 => array (
                'actionId' => '10',
                'parentId' => '9',
                'inputGroup' => 'productUrl',
                'type' => '13',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2 Go to produtct  page',
                'data' => NULL,
                ),
            6 => array (
                'actionId' => '11',
                'parentId' => '10',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.1 extract product images section',
                'data' => '<div class="ProductThumbImage" style="[^"]+?">.+?</div>',
                ),
            7 => array (
                'actionId' => '12',
                'parentId' => '11',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.1.1 extract big image src',
                'data' => 'href="(?P<productImgBigSrc>.+?)\\?c=2"',
                ),
            8 => array (
                'actionId' => '13',
                'parentId' => '11',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.1.2 extract image alt',
                'data' => 'alt="(?P<productImgAlt>[^"]+?)"',
                ),
            9 => array (
                'actionId' => '14',
                'parentId' => '11',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.1.3 extract medium image src',
                'data' => 'src="(?P<productImgMedSrc>[^"]+?)\\?c=2"',
                ),
            10 => array (
                'actionId' => '15',
                'parentId' => '10',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.2 extract product title',
                'data' => '<h1>(?P<productTitle>[^<]+?)</h1>',
                ),
            11 => array (
                'actionId' => '16',
                'parentId' => '10',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.3 extract product price',
                'data' => '<em class="ProductPrice VariationProductPrice">(?P<productPrice>[^<]+?)</em>',
                ),
            12 => array (
                'actionId' => '17',
                'parentId' => '10',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.5 extract product selected price weight',
                'data' => '<input[^a-z]+?type="radio".+?checked="checked"[^>]+?>[^<]+?<span [^>]+?>(?P<productSelectedWeight>[0-9]+?)(?P<productSelectedWeightUnit>[a-z]+?)</span>',
                ),
            13 => array (
                'actionId' => '18',
                'parentId' => '10',
                'inputGroup' => '0',
                'type' => '12',
                'useMatchAll' => '0',
                'isOpt' => '0',
                'title' => '2.6 extract product description',
                'data' => '<div class="ProductDescriptionContainer prodAccordionContent">[^<]+?<p><span style="font-size: small;">(?P<productDescrption>[^<]+?)</span></p>[^<]+?</div>',
                ),
           ); 
	}

	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getActionCallable($actionId)
	{
        if (in_array((integer)$actionId, array(6, 10))) {
            return array();
        }
		return $this->getResultSet("SELECT c.methodName AS methodName,
                                           c.serviceIdentifier AS serviceIdentifier
										FROM BAction_r_Callable AS b 
                                           INNER_JOIN Callable AS c ON b.callableId = c.callableId
                                        WHERE c.bActionId = :bActionId",
									array(':bActionId' => $actionId));
	}

	/**
	 * 
	 * @param unknown_type $actionId
	 * @return unknown_type
	 */
	public function getActionCallableParamsToGroupMapping($actionId)
	{
		$sql = "SELECT d.regexGroup AS regexGroup,
					   d.paramNum AS paramNum
					FROM BAction_RegexGroup_r_Callable_ParamNum AS d 
					WHERE d.bActionId = :bActionId 
					ORDER BY d.paramNum ASC";
		return $this->getResultSet($sql, array(':bActionId' => $actionId));
	}
	
	/**
	 * 
	 * @param unknown_type $actionId
	 * @return array
	 */
	public function getActionGroupToCallableAndInterceptType($actionId)
	{
        if (in_array((integer)$actionId, array(7, 8, 19, 9, 11, 12, 13, 14, 15, 16, 17, 18))) {
            return array();
        }
        throw new Exception('out of range');
		$sql = "SELECT c.methodName AS methodName,
                       c.serviceIdentifier AS serviceIdentifier,
					   b.regexGroup AS regexGroup,
					   b.interceptType AS interceptType
					FROM Callable as c
						LEFT JOIN BAction_RegexGroup_r_Callable as b
							ON (m.callableId = b.callableId)
					WHERE b.bActionId = :actionId
					ORDER BY b.interceptType ASC, c.serviceIdentifier ASC, c.methodName ASC, b.regexGroup ASC";
		return $this->getResultSet($sql, array(':actionId' => $actionId));
	}
}
